import { render } from "@testing-library/react";
import Sto from "../Sto";

test("renders Sto component", () => {
  const { baseElement } = render(<Sto />);
  expect(baseElement).toBeTruthy();
});
