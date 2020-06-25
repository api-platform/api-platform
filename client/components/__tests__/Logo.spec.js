import React from "react";
import { render } from "@testing-library/react";
import Logo from "../Logo";

test("renders deploy link", () => {
  const { baseElement } = render(<Logo />);
  expect(baseElement).toBeTruthy();
});
