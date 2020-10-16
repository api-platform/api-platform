import { render } from "@testing-library/react";
import Home from "../index";

test("renders home page", () => {
  const { baseElement } = render(<Home />);
  expect(baseElement).toBeTruthy();
});
