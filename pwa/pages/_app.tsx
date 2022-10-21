import "../styles/globals.css"
import Layout from "../components/common/Layout"
import type { AppProps } from "next/app"
import type { DehydratedState } from "react-query"

function MyApp({ Component, pageProps }: AppProps<{dehydratedState: DehydratedState}>) {
  return <Layout dehydratedState={pageProps.dehydratedState}>
    <Component {...pageProps} />
  </Layout>
}

export default MyApp
