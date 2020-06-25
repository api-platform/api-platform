import React from "react";
import { render } from "@testing-library/react";
import Arrow from "../Arrow";

test("renders deploy link", () => {
  const { baseElement } = render(<Arrow />);
  expect(baseElement).toBeTruthy();
});
