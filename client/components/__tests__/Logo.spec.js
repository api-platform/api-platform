import { render } from "@testing-library/react";
import Logo from "../Logo";

test("renders logo component", () => {
  const { baseElement } = render(<Logo />);
  expect(baseElement).toBeTruthy();
});
