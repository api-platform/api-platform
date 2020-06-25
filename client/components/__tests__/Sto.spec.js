import React from "react";
import { render } from "@testing-library/react";
import Sto from "../Sto";

test("renders deploy link", () => {
  const { baseElement } = render(<Sto />);
  expect(baseElement).toBeTruthy();
});
