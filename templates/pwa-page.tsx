"use client";

import { useEffect, useState } from "react";
import { parseHydraDocumentation } from "@api-platform/api-doc-parser";

const API_ENTRYPOINT =
  process.env.NEXT_PUBLIC_API_ENTRYPOINT || "https://localhost";

interface Resource {
  name: string;
  url: string;
  fields: { name: string; range: string | null; required: boolean }[];
  operations: { method: string; returns: string | null }[];
}

function ApiResources() {
  const [resources, setResources] = useState<Resource[]>([]);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    parseHydraDocumentation(API_ENTRYPOINT)
      .then(({ api }) => {
        setResources(
          (api.resources ?? []).map((r) => ({
            name: r.name,
            url: r.url,
            fields: (r.fields ?? []).map((f) => ({
              name: f.name,
              range: f.range ? String(f.range) : null,
              required: f.required,
            })),
            operations: (r.operations ?? []).map((op) => ({
              method: op.method ?? "GET",
              returns: op.returns ? String(op.returns) : null,
            })),
          })),
        );
      })
      .catch((e) => setError(e.message))
      .finally(() => setLoading(false));
  }, []);

  if (loading)
    return <p className="ap-loading">Connecting to API&hellip;</p>;
  if (error) {
    return (
      <div className="ap-error">
        <p>
          Could not reach the API at <code>{API_ENTRYPOINT}</code>.
        </p>
        <p>
          Make sure the API is running:{" "}
          <code>cd api && docker compose up --wait</code>
        </p>
        <details>
          <summary>Error details</summary>
          <pre>{error}</pre>
        </details>
      </div>
    );
  }

  if (resources.length === 0) {
    return (
      <div className="ap-empty">
        <p>No API resources found. Create your first entity to get started!</p>
        <pre>
          <code>
            {`// api/src/Entity/Book.php
use ApiPlatform\\Metadata\\ApiResource;
use Doctrine\\ORM\\Mapping as ORM;

#[ORM\\Entity]
#[ApiResource]
class Book
{
    #[ORM\\Id, ORM\\GeneratedValue, ORM\\Column]
    public ?int $id = null;

    #[ORM\\Column]
    public string $title = '';

    #[ORM\\Column]
    public string $author = '';
}`}
          </code>
        </pre>
      </div>
    );
  }

  return (
    <div className="ap-resources">
      <h2>API Resources</h2>
      <div className="ap-resource-grid">
        {resources.map((r) => (
          <div key={r.name} className="ap-resource-card">
            <h3>{r.name}</h3>
            <p className="ap-resource-url">{r.url}</p>
            {r.fields.length > 0 && (
              <table>
                <thead>
                  <tr>
                    <th>Field</th>
                    <th>Type</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  {r.fields.map((f) => (
                    <tr key={f.name}>
                      <td>
                        <code>{f.name}</code>
                      </td>
                      <td className="ap-field-type">
                        {f.range?.split("/").pop() ?? "unknown"}
                      </td>
                      <td>
                        {f.required ? (
                          <span className="ap-required">required</span>
                        ) : null}
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
            <div className="ap-operations">
              {r.operations.map((op, i) => (
                <span
                  key={i}
                  className={`ap-method ap-method-${op.method.toLowerCase()}`}
                >
                  {op.method}
                </span>
              ))}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}

const codeExamples = {
  zod: `import { createSchemas } from "@api-platform/zod";

const { schemas } = await createSchemas("${API_ENTRYPOINT}");

// Validate API responses at runtime
const result = schemas.Book.safeParse(apiResponse);
if (result.success) {
  console.log(result.data.title);
}`,
  ld: `import ld from "@api-platform/ld";

// Automatically resolve linked data (IRIs)
const books = await ld("/books", {
  onUpdate: (newBooks) => {
    // Author IRIs are resolved automatically
    console.log(newBooks[0].author?.name);
  },
});`,
  mercure: `import mercure from "@api-platform/mercure";

// Real-time updates via Mercure
const res = await mercure("${API_ENTRYPOINT}/books/1", {
  onUpdate: (book) => console.log("Updated:", book.title),
});`,
};

export default function Home() {
  const [activeTab, setActiveTab] =
    useState<keyof typeof codeExamples>("zod");

  return (
    <>
      <style>{cssStyles}</style>
      <main className="ap-container">
        <header className="ap-header">
          <div className="ap-logo">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 100 100"
              width="56"
              height="56"
            >
              <circle cx="50" cy="50" r="45" fill="#4eb7bc" opacity="0.2" />
              <circle cx="50" cy="50" r="30" fill="#0099a1" opacity="0.4" />
              <circle cx="50" cy="50" r="12" fill="#0099a1" />
            </svg>
          </div>
          <h1>API Platform</h1>
        </header>

        <section className="ap-links">
          <a
            href={`${API_ENTRYPOINT}/docs`}
            target="_blank"
            rel="noopener noreferrer"
            className="ap-btn ap-btn-primary"
          >
            API Docs
          </a>
          <a
            href="https://api-platform.com/docs/"
            target="_blank"
            rel="noopener noreferrer"
            className="ap-btn ap-btn-outline"
          >
            Documentation
          </a>
          <a
            href="https://github.com/api-platform"
            target="_blank"
            rel="noopener noreferrer"
            className="ap-btn ap-btn-outline"
          >
            GitHub
          </a>
        </section>

        <ApiResources />

        <section className="ap-libraries">
          <h2>Frontend Libraries</h2>
          <p>
            These libraries are pre-installed and ready to use in this project.
          </p>
          <div className="ap-tabs">
            {(
              Object.keys(codeExamples) as (keyof typeof codeExamples)[]
            ).map((tab) => (
              <button
                key={tab}
                className={activeTab === tab ? "ap-tab-active" : ""}
                onClick={() => setActiveTab(tab)}
              >
                @api-platform/{tab}
              </button>
            ))}
          </div>
          <pre className="ap-code">
            <code>{codeExamples[activeTab]}</code>
          </pre>
        </section>
      </main>
    </>
  );
}

const cssStyles = `
  /* API Platform theme colors:
     blue-black:     #0d262b
     blue-dark:      #00555a
     blue:           #0099a1
     blue-light:     #4eb7bc
     blue-extralight:#a3d2d4
  */
  .ap-container {
    max-width: 860px;
    margin: 0 auto;
    padding: 3rem 1.5rem;
    font-family: 'Poppins', ui-sans-serif, system-ui, sans-serif;
    color: #0d262b;
  }
  @media (prefers-color-scheme: dark) {
    .ap-container { color: #e8f4f5; }
  }
  .ap-header {
    text-align: center;
    margin-bottom: 2.5rem;
  }
  .ap-logo {
    margin-bottom: 0.5rem;
  }
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
  .ap-subtitle {
    color: #00555a;
    font-size: 1.15rem;
    margin-top: 0.5rem;
    font-weight: 400;
  }
  @media (prefers-color-scheme: dark) {
    .ap-subtitle { color: #a3d2d4; }
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
  .ap-btn-primary {
    background: #00555a;
    color: #fff;
    border: 2px solid #00555a;
  }
  .ap-btn-primary:hover {
    background: #0099a1;
    border-color: #0099a1;
  }
  .ap-btn-outline {
    background: transparent;
    color: #0099a1;
    border: 2px solid #a3d2d4;
  }
  .ap-btn-outline:hover {
    background: #0099a1;
    color: #fff;
    border-color: #0099a1;
  }
  @media (prefers-color-scheme: dark) {
    .ap-btn-primary { background: #0099a1; border-color: #0099a1; }
    .ap-btn-primary:hover { background: #4eb7bc; border-color: #4eb7bc; }
    .ap-btn-outline { color: #4eb7bc; border-color: #00555a; }
    .ap-btn-outline:hover { background: #00555a; color: #fff; border-color: #00555a; }
  }
  h2 {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #0d262b;
  }
  @media (prefers-color-scheme: dark) {
    h2 { color: #e8f4f5; }
  }
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
  .ap-resource-card h3 {
    margin: 0 0 0.25rem;
    color: #0099a1;
    font-weight: 600;
  }
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
  table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
  }
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
  @media (prefers-color-scheme: dark) {
    .ap-field-type { color: #a3d2d4; }
  }
  .ap-required { font-size: 0.75rem; color: #c2410c; font-weight: 600; }
  @media (prefers-color-scheme: dark) {
    .ap-required { color: #fb923c; }
  }
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
  @media (prefers-color-scheme: dark) {
    .ap-libraries > p { color: #a3d2d4; }
  }
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
  }
  @media (prefers-color-scheme: dark) {
    .ap-code { background: #00191c; border-color: #00555a; }
  }
  code { font-family: 'Fira Code', 'Cascadia Code', monospace; }
  .ap-loading {
    text-align: center;
    color: #0099a1;
    padding: 2rem;
    font-weight: 500;
  }
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
  @media (prefers-color-scheme: dark) {
    .ap-error code { color: #f9a8d4; }
  }
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
`;
