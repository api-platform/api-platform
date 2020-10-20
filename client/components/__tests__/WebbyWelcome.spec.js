import { render } from "@testing-library/react";
import WebbyWelcome from "../WebbyWelcome";

test("renders webby welcome", () => {
  const { baseElement } = render(<WebbyWelcome />);
  expect(baseElement).toBeTruthy();
});
