import React from "react";
import { render } from "@testing-library/react";
import Flag from "../Flag";

test("renders deploy link", () => {
  const { baseElement } = render(<Flag />);
  expect(baseElement).toBeTruthy();
});
