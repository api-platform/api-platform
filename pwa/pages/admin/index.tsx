import Head from "next/head";

const API_ENTRYPOINT =
  process.env.REACT_APP_API_ENTRYPOINT || "https://localhost";

const AdminLoader = () => {
  if (typeof window !== "undefined") {
    const { HydraAdmin } = require("@api-platform/admin");
    return <HydraAdmin entrypoint={API_ENTRYPOINT} />;
  }

  return <></>;
};

const Admin = () => (
  <>
    <Head>
      <title>API Platform Admin</title>
    </Head>

    <AdminLoader />
  </>
);
export default Admin;
