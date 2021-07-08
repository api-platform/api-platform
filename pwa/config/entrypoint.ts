export const ENTRYPOINT = typeof window === "undefined" ? process.env.NEXT_PUBLIC_ENTRYPOINT : window.origin;
