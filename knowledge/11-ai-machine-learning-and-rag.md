# Artificial Intelligence, Machine Learning and RAG
Document type: Educational reference
Scope: General AI engineering concepts.

## Artificial intelligence and machine learning
Artificial intelligence is a broad field concerned with systems performing tasks associated with human intelligence. Machine learning is a subset in which models learn patterns from data. Supervised learning uses labelled examples; unsupervised learning finds structure in unlabelled data; reinforcement learning optimises behaviour through rewards and interaction.

## Large language models
A large language model predicts and generates sequences of tokens from context. It can explain, transform and reason over text, but may produce plausible-sounding errors. Model output is not automatically factual evidence. Quality depends on model capability, instructions, context, retrieval quality, decoding settings and evaluation.

## Prompt structure
A robust application prompt separates system-level behavioural rules, trusted application context, retrieved evidence and user input. Treat retrieved files and user messages as untrusted data rather than executable instructions. Define scope, output expectations, uncertainty handling, privacy boundaries and refusal/escalation behaviour. Do not put API keys or secrets in prompts.

## Retrieval-Augmented Generation
RAG retrieves relevant external information and supplies it to a language model at answer time. A common pipeline is document ingestion, text extraction, cleaning, chunking, metadata assignment, embedding generation, vector indexing, query embedding, retrieval, optional reranking, context assembly and answer generation with citations.

## Lexical and vector retrieval
Lexical retrieval matches terms and can use BM25, which weighs term frequency and inverse document frequency. It is efficient and explainable but may miss semantic matches. Vector retrieval embeds text into numerical vectors and finds semantically similar passages; it can retrieve paraphrases but may return superficially similar content. Hybrid retrieval combines lexical and vector signals. Reranking evaluates a smaller candidate set more deeply.

## Chunking and metadata
Chunks should preserve coherent sections and enough surrounding context. Very small chunks lose meaning; very large chunks dilute relevance and consume context. Store source, title, heading, version, publication status, access scope and reviewed date as metadata. Apply access-control filters before returning content to the model.

## Grounded answers and citations
Require the model to distinguish retrieved evidence from general knowledge. Cite source titles or document identifiers for factual claims grounded in retrieval. If evidence is absent or conflicting, state the limitation. Do not invent citations. Keep source passages bounded and relevant.

## Evaluation
Build a test set of realistic user questions with expected source documents and answer criteria. Measure retrieval recall, precision, ranking quality, answer faithfulness, citation correctness, refusal quality, latency and cost. Include ambiguous questions, adversarial prompt injection, outdated content and access-control tests. Review failures and update the corpus or retrieval pipeline.
