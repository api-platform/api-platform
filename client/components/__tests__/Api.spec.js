import { render } from "@testing-library/react";
import Api from "../Api";

test("renders Api component", () => {
  const { baseElement } = render(<Api />);
  expect(baseElement).toBeTruthy();
});
