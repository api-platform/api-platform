<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Platform</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* API Platform theme:
           blue-black:     #0d262b
           blue-dark:      #00555a
           blue:           #0099a1
           blue-light:     #4eb7bc
           blue-extralight:#a3d2d4 */
        body { margin: 0; }
        .ap-container {
            max-width: 860px;
            margin: 0 auto;
            padding: 3rem 1.5rem;
            font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
            color: #0d262b;
        }
        @media (prefers-color-scheme: dark) {
            body { background: #00191c; }
            .ap-container { color: #e8f4f5; }
        }
        .ap-header { text-align: center; margin-bottom: 2.5rem; }
        .ap-logo { margin-bottom: 0.5rem; }
        .ap-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #0d262b;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }
        @media (prefers-color-scheme: dark) {
            .ap-header h1 { color: #fff; }
        }
        .ap-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
        }
        .ap-btn {
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 1.5rem;
            border-radius: 24px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            transition: background 0.2s, opacity 0.2s;
        }
        .ap-btn-primary { background: #00555a; color: #fff; border: 2px solid #00555a; }
        .ap-btn-primary:hover { background: #0099a1; border-color: #0099a1; }
        .ap-btn-outline { background: transparent; color: #0099a1; border: 2px solid #a3d2d4; }
        .ap-btn-outline:hover { background: #0099a1; color: #fff; border-color: #0099a1; }
        @media (prefers-color-scheme: dark) {
            .ap-btn-primary { background: #0099a1; border-color: #0099a1; }
            .ap-btn-primary:hover { background: #4eb7bc; border-color: #4eb7bc; }
            .ap-btn-outline { color: #4eb7bc; border-color: #00555a; }
            .ap-btn-outline:hover { background: #00555a; color: #fff; border-color: #00555a; }
        }
        h2 { font-size: 1.4rem; font-weight: 600; margin-bottom: 1rem; color: #0d262b; }
        @media (prefers-color-scheme: dark) { h2 { color: #e8f4f5; } }
        .ap-resource-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        }
        .ap-resource-card {
            border: 1px solid #a3d2d4;
            border-radius: 12px;
            padding: 1.25rem;
            background: #f0fafa;
        }
        @media (prefers-color-scheme: dark) {
            .ap-resource-card { border-color: #00555a; background: #0d262b; }
        }
        .ap-resource-card h3 { margin: 0 0 0.25rem; color: #0099a1; font-weight: 600; }
        @media (prefers-color-scheme: dark) {
            .ap-resource-card h3 { color: #4eb7bc; }
        }
        .ap-resource-url {
            font-size: 0.85rem;
            color: #00555a;
            margin: 0 0 0.75rem;
            font-family: 'Fira Code', 'Cascadia Code', monospace;
            opacity: 0.7;
        }
        @media (prefers-color-scheme: dark) {
            .ap-resource-url { color: #a3d2d4; }
        }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; margin-bottom: 0.75rem; }
        th {
            text-align: left;
            color: #00555a;
            font-weight: 500;
            padding: 0.3rem 0.5rem;
            border-bottom: 1px solid #a3d2d4;
        }
        @media (prefers-color-scheme: dark) {
            th { color: #a3d2d4; border-color: #00555a; }
        }
        td { padding: 0.3rem 0.5rem; }
        .ap-field-type { color: #00555a; font-size: 0.8rem; }
        @media (prefers-color-scheme: dark) { .ap-field-type { color: #a3d2d4; } }
        .ap-required { font-size: 0.75rem; color: #c2410c; font-weight: 600; }
        @media (prefers-color-scheme: dark) { .ap-required { color: #fb923c; } }
        .ap-operations { display: flex; gap: 0.4rem; flex-wrap: wrap; }
        .ap-method {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-family: monospace;
            text-transform: uppercase;
        }
        .ap-method-get { background: #d1fae5; color: #065f46; }
        .ap-method-post { background: #dbeafe; color: #1e40af; }
        .ap-method-put, .ap-method-patch { background: #fef3c7; color: #92400e; }
        .ap-method-delete { background: #fee2e2; color: #991b1b; }
        @media (prefers-color-scheme: dark) {
            .ap-method-get { background: #065f46; color: #6ee7b7; }
            .ap-method-post { background: #1e3a5f; color: #93c5fd; }
            .ap-method-put, .ap-method-patch { background: #713f12; color: #fcd34d; }
            .ap-method-delete { background: #7f1d1d; color: #fca5a5; }
        }
        .ap-libraries { margin-top: 2.5rem; }
        .ap-libraries > p { color: #00555a; margin-bottom: 1rem; }
        @media (prefers-color-scheme: dark) { .ap-libraries > p { color: #a3d2d4; } }
        .ap-tabs { display: flex; gap: 0; }
        .ap-tabs button {
            background: #e8f4f5;
            color: #00555a;
            border: 1px solid #a3d2d4;
            border-bottom: none;
            padding: 0.5rem 1rem;
            cursor: pointer;
            font-family: 'Fira Code', 'Cascadia Code', monospace;
            font-size: 0.85rem;
            border-radius: 8px 8px 0 0;
            transition: background 0.2s, color 0.2s;
            font-weight: 500;
        }
        .ap-tabs button:hover { background: #d0eced; }
        .ap-tabs button.ap-tab-active {
            background: #fff;
            color: #0099a1;
            border-color: #0099a1;
            font-weight: 600;
        }
        @media (prefers-color-scheme: dark) {
            .ap-tabs button { background: #0d262b; color: #a3d2d4; border-color: #00555a; }
            .ap-tabs button:hover { background: #00555a; }
            .ap-tabs button.ap-tab-active { background: #00191c; color: #4eb7bc; border-color: #0099a1; }
        }
        .ap-code {
            background: #f0fafa;
            border: 1px solid #a3d2d4;
            border-radius: 0 8px 8px 8px;
            padding: 1.25rem;
            overflow-x: auto;
            font-size: 0.85rem;
            line-height: 1.6;
            margin-top: 0;
            white-space: pre;
        }
        @media (prefers-color-scheme: dark) {
            .ap-code { background: #00191c; border-color: #00555a; }
        }
        code { font-family: 'Fira Code', 'Cascadia Code', monospace; }
        .ap-loading { text-align: center; color: #0099a1; padding: 2rem; font-weight: 500; }
        .ap-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 1.25rem;
            color: #991b1b;
        }
        @media (prefers-color-scheme: dark) {
            .ap-error { background: #2d1215; border-color: #7f1d1d; color: #fca5a5; }
        }
        .ap-error code { color: #be185d; }
        @media (prefers-color-scheme: dark) { .ap-error code { color: #f9a8d4; } }
        .ap-empty {
            background: #f0fafa;
            border: 1px solid #a3d2d4;
            border-radius: 12px;
            padding: 1.25rem;
            color: #00555a;
        }
        @media (prefers-color-scheme: dark) {
            .ap-empty { background: #0d262b; border-color: #00555a; color: #a3d2d4; }
        }
    </style>
</head>
<body>
    <main class="ap-container">
        <header class="ap-header">
            <div class="ap-logo">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="56" height="56">
                    <circle cx="50" cy="50" r="45" fill="#4eb7bc" opacity="0.2"/>
                    <circle cx="50" cy="50" r="30" fill="#0099a1" opacity="0.4"/>
                    <circle cx="50" cy="50" r="12" fill="#0099a1"/>
                </svg>
            </div>
            <h1>API Platform</h1>
        </header>

        <section class="ap-links">
            <a href="/api/docs" target="_blank" rel="noopener noreferrer" class="ap-btn ap-btn-primary">API Docs</a>
            <a href="https://api-platform.com/docs/" target="_blank" rel="noopener noreferrer" class="ap-btn ap-btn-outline">Documentation</a>
            <a href="https://github.com/api-platform" target="_blank" rel="noopener noreferrer" class="ap-btn ap-btn-outline">GitHub</a>
        </section>

        <section id="ap-resources" class="ap-resources-mount">
            <p class="ap-loading">Connecting to API&hellip;</p>
        </section>

        <section class="ap-libraries">
            <h2>Frontend Libraries</h2>
            <p>These libraries are pre-installed and ready to use in this project.</p>
            <div class="ap-tabs" data-ap-tabs>
                <button type="button" class="ap-tab-active" data-tab="zod">@api-platform/zod</button>
                <button type="button" data-tab="ld">@api-platform/ld</button>
                <button type="button" data-tab="mercure">@api-platform/mercure</button>
            </div>
            <pre class="ap-code"><code data-ap-snippet>import { createSchemas } from "@api-platform/zod";

const { schemas } = await createSchemas("/api");

// Validate API responses at runtime
const result = schemas.Book.safeParse(apiResponse);
if (result.success) {
  console.log(result.data.title);
}</code></pre>
        </section>
    </main>
</body>
</html>
