import { render } from "@testing-library/react";
import Flag from "../Flag";

test("renders Flag component", () => {
  const { baseElement } = render(<Flag />);
  expect(baseElement).toBeTruthy();
});
