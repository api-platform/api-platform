module.exports = {
  collectCoverageFrom: [
    "**/*.{js,jsx,ts,tsx}",
    "!**/*.d.ts",
    "!**/.yarn/**",
  ],
  setupFilesAfterEnv: ["./setupTests.js"],
  testPathIgnorePatterns: [
    "/.yarn/",
    "/.next/"
  ],
  transform: {
    "^.+\\.[jt]sx?$": "babel-jest",
    "^.+\\.css$": "./config/jest/cssTransform.js",
  },
  transformIgnorePatterns: [
    "^.+\\.pnp\\.[^\\/]+$",
    "^.+\\.module\\.(css|sass|scss)$",
  ],
  moduleNameMapper: {
    "^.+\\.module\\.(css|sass|scss)$": "identity-obj-proxy",
  },
};
