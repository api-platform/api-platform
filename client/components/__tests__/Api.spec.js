import React from "react";
import { render } from "@testing-library/react";
import Api from "../Api";

test("renders deploy link", () => {
  const { baseElement } = render(<Api />);
  expect(baseElement).toBeTruthy();
});
