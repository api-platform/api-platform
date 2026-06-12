import { parseHydraDocumentation } from "@api-platform/api-doc-parser";

// Must be absolute: parseHydraDocumentation passes it to jsonld.expand as
// `base`, which rejects relative URLs.
const API_ENTRYPOINT = `${window.location.origin}/api`;

const SNIPPETS = {
    zod: `import { createSchemas } from "@api-platform/zod";

const { schemas } = await createSchemas("${API_ENTRYPOINT}");

// Validate API responses at runtime
const result = schemas.Book.safeParse(apiResponse);
if (result.success) {
  console.log(result.data.title);
}`,
    ld: `import ld from "@api-platform/ld";

// Automatically resolve linked data (IRIs)
const books = await ld("${API_ENTRYPOINT}/books", {
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

function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, (c) => ({
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
    })[c]);
}

function renderResources(resources, mount) {
    if (resources.length === 0) {
        mount.innerHTML = `
            <div class="ap-empty">
                <p>No API resources found. Create your first model to get started!</p>
                <pre><code>// app/Models/Book.php
namespace App\\Models;

use ApiPlatform\\Metadata\\ApiResource;
use Illuminate\\Database\\Eloquent\\Model;

#[ApiResource]
class Book extends Model
{
    protected $fillable = ['title', 'author'];
}</code></pre>
            </div>`;
        return;
    }

    const cards = resources.map((r) => {
        const fieldsHtml = (r.fields ?? []).map((f) => {
            const range = f.range ? String(f.range).split("/").pop() : "unknown";
            const required = f.required
                ? '<span class="ap-required">required</span>'
                : "";
            return `<tr>
                <td><code>${escapeHtml(f.name)}</code></td>
                <td class="ap-field-type">${escapeHtml(range)}</td>
                <td>${required}</td>
            </tr>`;
        }).join("");

        const operationsHtml = (r.operations ?? []).map((op) => {
            const method = (op.method ?? "GET").toLowerCase();
            return `<span class="ap-method ap-method-${escapeHtml(method)}">${escapeHtml(method.toUpperCase())}</span>`;
        }).join("");

        return `<div class="ap-resource-card">
            <h3>${escapeHtml(r.name)}</h3>
            <p class="ap-resource-url">${escapeHtml(r.url)}</p>
            ${fieldsHtml ? `<table>
                <thead><tr><th>Field</th><th>Type</th><th></th></tr></thead>
                <tbody>${fieldsHtml}</tbody>
            </table>` : ""}
            <div class="ap-operations">${operationsHtml}</div>
        </div>`;
    }).join("");

    mount.innerHTML = `<h2>API Resources</h2><div class="ap-resource-grid">${cards}</div>`;
}

function formatError(err) {
    if (err instanceof Error) return err.message;
    // parseHydraDocumentation rejects with a plain object: { api, error, response, status }.
    if (err && typeof err === "object") {
        if (err.error instanceof Error) return err.error.message;
        if (typeof err.message === "string") return err.message;
        try {
            return JSON.stringify(err, null, 2);
        } catch {
            return Object.prototype.toString.call(err);
        }
    }
    return String(err);
}

function renderError(message, mount) {
    mount.innerHTML = `
        <div class="ap-error">
            <p>Could not reach the API at <code>${escapeHtml(API_ENTRYPOINT)}</code>.</p>
            <p>Make sure the API is running: <code>php artisan serve</code></p>
            <details>
                <summary>Error details</summary>
                <pre>${escapeHtml(message)}</pre>
            </details>
        </div>`;
}

function wireTabs() {
    const tabs = document.querySelector("[data-ap-tabs]");
    const snippet = document.querySelector("[data-ap-snippet]");
    if (!tabs || !snippet) return;

    tabs.addEventListener("click", (event) => {
        const target = event.target;
        if (!(target instanceof HTMLButtonElement)) return;
        const key = target.dataset.tab;
        if (!key || !(key in SNIPPETS)) return;

        for (const btn of tabs.querySelectorAll("button")) {
            btn.classList.toggle("ap-tab-active", btn === target);
        }
        snippet.textContent = SNIPPETS[key];
    });
}

async function bootstrap() {
    wireTabs();
    const mount = document.getElementById("ap-resources");
    if (!mount) return;

    try {
        const { api } = await parseHydraDocumentation(API_ENTRYPOINT);
        const resources = (api.resources ?? []).map((r) => ({
            name: r.name,
            url: r.url,
            fields: (r.fields ?? []).map((f) => ({
                name: f.name,
                range: f.range ? String(f.range) : null,
                required: f.required,
            })),
            operations: (r.operations ?? []).map((op) => ({
                method: op.method ?? "GET",
            })),
        }));
        renderResources(resources, mount);
    } catch (error) {
        renderError(formatError(error), mount);
    }
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", bootstrap);
} else {
    bootstrap();
}
