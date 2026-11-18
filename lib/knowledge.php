<?php
declare(strict_types=1);

/**
 * Local hybrid lexical retrieval for curated Markdown knowledge.
 * No external service or Composer package is required. The result contract is
 * intentionally small so a vector-search adapter can replace this later.
 */
function kb_tokenize(string $text): array {
    $text = mb_strtolower(strip_tags($text), 'UTF-8');
    preg_match_all('/[\p{L}\p{N}]{2,}/u', $text, $matches);
    $stop = array_flip([
        'the','and','for','with','that','this','from','your','you','are','how','what',
        'when','where','who','why','can','could','would','should','into','about','have',
        'has','was','were','will','their','there','them','then','than','also','use',
        'using','please','does','did','not','but','its','our','out','get','make','let',
        'than','they','his','her','she','him','all','any','which','such','more','most',
        'some','been','being','each','other','only','very','just','like','want','need'
    ]);
    return array_values(array_filter($matches[0] ?? [], static fn($t) => !isset($stop[$t])));
}

/** Expand common technical synonyms without replacing the user's original terms. */
function kb_expand_terms(array $terms): array {
    $groups = [
        ['api','endpoint','rest','http','webhook'],
        ['database','db','sql','mysql','mariadb','postgres','postgresql'],
        ['frontend','front-end','react','javascript','typescript','ui','interface'],
        ['backend','back-end','laravel','php','server'],
        ['security','secure','csrf','xss','injection','authentication','authorisation','authorization'],
        ['course','module','lesson','learning','academic','study'],
        ['quiz','assessment','test','exam','grading','score'],
        ['certificate','certification','credential'],
        ['ai','artificial','intelligence','llm','model','chatbot','assistant','rag','retrieval'],
        ['network','networking','dns','tcp','udp','ip','router'],
        ['deploy','deployment','hosting','production','server','release'],
        ['debug','debugging','error','exception','troubleshoot','troubleshooting'],
        ['git','github','version','control','commit','branch','repository'],
        ['file','document','attachment','pdf','upload','material','resource']
    ];
    $set = array_fill_keys($terms, true);
    foreach ($groups as $group) {
        if (count(array_intersect($terms, $group)) > 0) {
            foreach ($group as $term) $set[$term] = true;
        }
    }
    return array_keys($set);
}

function kb_read_documents(): array {
    $dir = dirname(__DIR__) . '/knowledge';
    if (!is_dir($dir)) return [];
    $docs = [];
    foreach (glob($dir . '/*.md') ?: [] as $file) {
        $raw = file_get_contents($file);
        if ($raw === false || trim($raw) === '') continue;
        $title = preg_replace('/^\d+-/', '', basename($file, '.md')) ?? basename($file, '.md');
        $title = ucwords(str_replace(['-', '_'], ' ', $title));
        $section = $title;
        $buffer = [];
        $flush = static function() use (&$buffer, &$docs, $file, &$section, $title): void {
            $text = trim(implode("\n", $buffer));
            $buffer = [];
            if (mb_strlen($text, 'UTF-8') < 55) return;
            $docs[] = [
                'source' => basename($file),
                'title' => $title,
                'section' => $section,
                'text' => $text,
            ];
        };
        foreach (preg_split('/\R/u', $raw) ?: [] as $line) {
            if (preg_match('/^#{1,3}\s+(.+)/u', $line, $m)) {
                $flush();
                $section = trim($m[1]);
                $buffer[] = $line;
                continue;
            }
            $buffer[] = $line;
            // Keep chunks coherent and bounded for prompt context.
            if (mb_strlen(implode("\n", $buffer), 'UTF-8') >= 1800) $flush();
        }
        $flush();
    }
    return $docs;
}

function kb_retrieve(string $query, int $limit = 7): array {
    static $documents = null;
    if ($documents === null) $documents = kb_read_documents();
    if (!$documents) return [];

    $originalTerms = array_values(array_unique(kb_tokenize($query)));
    if (!$originalTerms) return [];
    $queryTerms = kb_expand_terms($originalTerms);
    $queryText = mb_strtolower(trim($query), 'UTF-8');

    $N = count($documents);
    $df = [];
    $lengths = [];
    $termSets = [];
    foreach ($documents as $i => $doc) {
        $terms = kb_tokenize($doc['title'].' '.$doc['section'].' '.$doc['text']);
        $termSets[$i] = $terms;
        $lengths[$i] = count($terms);
        foreach (array_unique($terms) as $term) $df[$term] = ($df[$term] ?? 0) + 1;
    }
    $avgdl = max(1.0, array_sum($lengths) / max(1, $N));
    $scored = [];

    foreach ($documents as $i => $doc) {
        $fieldTitle = kb_tokenize($doc['title']);
        $fieldHeading = kb_tokenize($doc['section']);
        $terms = $termSets[$i];
        $tf = array_count_values($terms);
        $score = 0.0;
        $matched = 0;
        foreach ($queryTerms as $term) {
            if (!isset($tf[$term]) && !in_array($term, $fieldHeading, true) && !in_array($term, $fieldTitle, true)) continue;
            $matched++;
            $frequency = (float)($tf[$term] ?? 0);
            $idf = log(1 + ($N - ($df[$term] ?? 0) + 0.5) / (($df[$term] ?? 0) + 0.5));
            $denom = $frequency + 1.2 * (0.25 + 0.75 * (count($terms) / $avgdl));
            $score += $idf * (($frequency * 2.2) / max(0.1, $denom));
            if (in_array($term, $fieldHeading, true)) $score += $idf * 1.35;
            if (in_array($term, $fieldTitle, true)) $score += $idf * 1.8;
        }
        if ($matched === 0) continue;

        // Reward complete phrase matches and query coverage; do not let one
        // repeated keyword dominate a document that misses the actual topic.
        $plain = mb_strtolower(strip_tags($doc['title'].' '.$doc['section'].' '.$doc['text']), 'UTF-8');
        if (mb_strlen($queryText, 'UTF-8') >= 5 && mb_strpos($plain, $queryText) !== false) $score += 2.5;
        $coverage = $matched / max(1, count($queryTerms));
        $score *= (0.65 + 0.7 * $coverage);
        if ($score > 0.15) {
            $doc['score'] = round($score, 4);
            $doc['coverage'] = round($coverage, 3);
            $scored[] = $doc;
        }
    }
    usort($scored, static fn($a, $b) => $b['score'] <=> $a['score']);
    return array_slice($scored, 0, max(1, min(12, $limit)));
}

function kb_context(array $results, int $maxChars = 18000): string {
    if (!$results) return '';
    $parts = [];
    $used = 0;
    foreach ($results as $i => $item) {
        $part = "[Evidence ".($i + 1).": {$item['source']} | {$item['section']}]\n{$item['text']}";
        $remaining = $maxChars - $used;
        if ($remaining <= 0) break;
        if (mb_strlen($part, 'UTF-8') > $remaining) {
            if ($remaining > 350) $parts[] = mb_substr($part, 0, $remaining, 'UTF-8');
            break;
        }
        $parts[] = $part;
        $used += mb_strlen($part, 'UTF-8');
    }
    return implode("\n\n---\n\n", $parts);
}
