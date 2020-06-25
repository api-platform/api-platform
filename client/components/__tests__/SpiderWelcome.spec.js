import React from "react";
import { render } from "@testing-library/react";
import SpiderWelcome from "../SpiderWelcome";

test("renders deploy link", () => {
  const { baseElement } = render(<SpiderWelcome />);
  expect(baseElement).toBeTruthy();
});
