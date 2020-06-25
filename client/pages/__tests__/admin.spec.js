import React from "react";
import { render } from "@testing-library/react";
import Admon from "../admin";

test("renders deploy link", () => {
  const { baseElement } = render(<Admon />);
  expect(baseElement).toBeTruthy();
});
