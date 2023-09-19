import Head from "next/head";
import React from "react";
import { fetchHydra, HydraAdmin, hydraDataProvider } from "@api-platform/admin";
import { parseHydraDocumentation } from "@api-platform/api-doc-parser";
import { ENTRYPOINT } from "config/entrypoint";
import { getSession, signIn, useSession } from "next-auth/react";
import authProvider from "./authProvider";

const getHeaders = async () => {
  const session = await getSession();

  return {
    // @ts-ignore
    Authorization: `Bearer ${session?.accessToken}`,
  };
};

const apiDocumentationParser = async () => {
  try {
    return await parseHydraDocumentation(ENTRYPOINT, { headers: getHeaders() });
  } catch (result) {
    const { api, response, status } = result;
    if (status !== 401 || !response) {
      throw result;
    }

    return {
      api,
      response,
      status,
    };
  }
};

const dataProvider = () => hydraDataProvider({
  useEmbedded: false,
  entrypoint: ENTRYPOINT,
  httpClient: (url: URL, options = {}) => fetchHydra(url, {
    ...options,
    // @ts-ignore
    headers: getHeaders(),
  }),
  apiDocumentationParser,
});

const Admin = () => {
  // Can't use next-auth/middleware because of https://github.com/nextauthjs/next-auth/discussions/7488
  const { data: session, status } = useSession();

  if (status === "loading") {
    return <p>Loading...</p>;
  }

  // @ts-ignore
  if (!session || session?.error === "RefreshAccessTokenError") {
    (async() => await signIn("keycloak"))();

    return;
  }

  return (
    <>
      <Head>
        <title>API Platform Admin</title>
      </Head>

      <HydraAdmin dataProvider={dataProvider()}
                  entrypoint={window.origin}
                  authProvider={authProvider}
                  requireAuth />
    </>
  );
}

export default Admin;
