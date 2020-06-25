import React from "react";
import { render } from "@testing-library/react";
import HelpButton from "../HelpButton";
import Sto from "../Sto";

test("renders deploy link", () => {
  const { baseElement } = render(
    <HelpButton
      url="https://stackoverflow.com/questions/tagged/api-platform.com"
      Image={Sto}
      title="Ask your questions on Stack Overflow!"
    />
  );
  expect(baseElement).toBeTruthy();
});
