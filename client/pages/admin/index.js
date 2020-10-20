const API_ENTRYPOINT =
  process.env.REACT_APP_API_ENTRYPOINT || "https://localhost:8443";

export default () => {
  if (typeof window !== "undefined") {
    const { HydraAdmin } = require("@api-platform/admin");
    return <HydraAdmin entrypoint={API_ENTRYPOINT} />;
  }

  return <div>Loading</div>;
};
