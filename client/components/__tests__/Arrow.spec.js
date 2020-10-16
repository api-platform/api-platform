import { render } from "@testing-library/react";
import Arrow from "../Arrow";

test("renders Arrow component", () => {
  const { baseElement } = render(<Arrow />);
  expect(baseElement).toBeTruthy();
});
