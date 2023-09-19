import "../styles/globals.css"
import Layout from "../components/common/Layout"
import type { AppProps } from "next/app"
import type { DehydratedState } from "react-query"
import { Session } from "next-auth"
import { SessionProvider } from "next-auth/react"

function MyApp({ Component, pageProps }: AppProps<{
  dehydratedState: DehydratedState,
  session: Session,
}>) {
  return (
    <SessionProvider session={pageProps.session}>
      <Layout dehydratedState={pageProps.dehydratedState}>
        <Component {...pageProps} />
      </Layout>
    </SessionProvider>
  )
}

export default MyApp
