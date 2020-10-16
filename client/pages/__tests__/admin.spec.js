import { render } from "@testing-library/react";
import Admin from "../admin";

test("renders admin page", () => {
  const { baseElement } = render(<Admin />);
  expect(baseElement).toBeTruthy();
});
