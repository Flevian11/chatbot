# Learn With Flevian LMS: Verified Repository Architecture
Document type: Repository-derived platform reference
Repository: Flevian11/lwf-lms
Branch inspected: main
Review date: 2026-11-18
Scope: Facts visible in the inspected repository source. This is not a substitute for live production configuration.

## Application architecture
The inspected repository is a Laravel application with a React and TypeScript frontend using Inertia.js and Vite. The student interface is organised under `resources/js/Pages/`. Backend HTTP controllers are under `app/Http/Controllers/`, Eloquent models under `app/Models/`, and application services under `app/Services/`. Tailwind CSS is used in the frontend styling.

## Learning content hierarchy
The repository defines a `Course` model with relationships to modules, enrollments, assignments, quizzes, lesson progress, learning activities and course materials. `CourseModule` belongs to a course and has ordered lessons. `Lesson` belongs to a module and stores fields including title, description, content, type, position, preview status, duration and publication status. `CourseMaterial` can be associated with a course, module or lesson and includes title, description, type, URL or file path, MIME type, preview flag, position, status and metadata.

## Student learning and assessment
The repository contains models and services for course enrollments, lesson progress, assignments, assignment submissions, quizzes, quiz attempts, student learning activity and achievements. The student chatbot service reads selected authenticated-student context such as active/completed enrollments, progress, allocated assignments and submissions, published quizzes and attempts, recent learning activity and earned achievements.

## Student chatbot implementation
The repository includes `app/Services/StudentChatbotService.php` and `app/Http/Controllers/StudentChatbotController.php`. The service builds a system prompt and student context, normalises recent conversation messages, reads OpenRouter configuration from `config/services.php`, submits a chat-completions request and returns the model response. The controller handles student chatbot pages, conversation persistence, message handling, rename and deletion.

## Important knowledge-retrieval limitation in the inspected LMS chatbot
The inspected `StudentChatbotService::buildStudentContext()` assembles student-specific learning and activity data. It does not, in the inspected implementation, retrieve the full text of `Lesson.content` or `CourseMaterial` documents as a course-content RAG corpus. Therefore, do not claim that the deployed LMS chatbot automatically reads every lesson, PDF, presentation or course material unless a newer implementation adds that retrieval path.

## Relevant repository paths
- `app/Services/StudentChatbotService.php`
- `app/Http/Controllers/StudentChatbotController.php`
- `app/Models/Course.php`
- `app/Models/CourseModule.php`
- `app/Models/Lesson.php`
- `app/Models/CourseMaterial.php`
- `resources/js/Pages/Chatbot.tsx`
- `resources/js/Components/StudentChatbotWidget.tsx`
- `resources/js/lib/chatbot.ts`
- `config/services.php`

## Accuracy rules
This document describes repository code, not necessarily the current production deployment. Do not infer live database contents, current user records, unpublished features, exact UI workflows, deployment secrets or live configuration from source code alone. Verify the current branch and deployed version before giving operational instructions.
