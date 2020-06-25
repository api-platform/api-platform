import React from "react";
import { render } from "@testing-library/react";
import Home from "../index";

test("renders deploy link", () => {
  const { baseElement } = render(<Home />);
  expect(baseElement).toBeTruthy();
});
