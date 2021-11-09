module.exports = {
  collectCoverageFrom: [
    "**/*.{js,jsx,ts,tsx}",
    "!**/*.d.ts",
    "!**/node_modules/**",
  ],
  setupFilesAfterEnv: ["./setupTests.js"],
  testPathIgnorePatterns: ["/node_modules/", "/.next/"],
  transform: {
    '^.+\\.(js|jsx|ts|tsx)$': [
      './node_modules/babel-jest',
      { configFile: './babel.jest.config.js' },
    ],
    "^.+\\.css$": "./config/jest/cssTransform.js",
  },
  transformIgnorePatterns: [
    "/node_modules/",
    "^.+\\.module\\.(css|sass|scss)$",
  ],
  moduleNameMapper: {
    "^.+\\.module\\.(css|sass|scss)$": "identity-obj-proxy",
  },
};
