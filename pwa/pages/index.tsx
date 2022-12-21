import Head from "next/head";
import Image from "next/image";
import Link from "next/link";
import React from "react";
import adminPicture from "../public/api-platform/admin.svg";
import rocketPicture from "../public/api-platform/rocket.svg";
import logo from "../public/api-platform/logo_api-platform.svg";
import mercurePicture from "../public/api-platform/mercure.svg";
import logoTilleuls from "../public/api-platform/logo_tilleuls.svg";
import apiPicture from "../public/api-platform/api.svg";
import "@fontsource/poppins";
import "@fontsource/poppins/600.css";
import "@fontsource/poppins/700.css";

const Welcome = () => (
  <div className="w-full overflow-x-hidden">
    <Head>
      <title>Welcome to API Platform!</title>
    </Head>
    <section className="w-full bg-spider-cover relative">
      <a
        href="https://les-tilleuls.coop/en"
        target="_blank"
        rel="noreferrer noopener"
        className="z-10 bg-black px-8 py-2 text-xs text-white ribbon | md:px-12"
      >
        <div className="flex flex-row justify-center items-center translate-x-[5%]">
          Made with
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            className="mx-2 w-5 h-5 fill-red-500"
          >
            <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
          </svg>
          by
          <div className="pl-2 flex items-center">
            <Image
              alt="Les-Tilleuls.coop"
              src={logoTilleuls}
              width={100}
              height={20}
            />
          </div>
        </div>
      </a>
      <div className="container flex flex-row pt-24 pb-8 | md:px-20">
        <div className="hidden relative h-full w-2/5 origin-right scale-150 | md:block | lg:scale-100">
          <div className="absolute">
            <Image src={rocketPicture} alt="" />
          </div>
        </div>
        <div className="flex flex-1 flex-col items-center text-center | md:text-left md:items-start">
          <h1>
            <span className="block text-4xl text-cyan-200 font-bold mb-2">
              Welcome to
            </span>
            <Image alt="API Platform" src={logo} />
          </h1>
          <p className="text-cyan-200 my-5 text-lg">
            This container will host your{" "}
            <a
              className="text-white font-bold hover:bg-cyan-500"
              href="https://nextjs.org/"
            >
              <b>Next.js</b>
            </a>{" "}
            application. Learn how to create your first API and generate a PWA:
          </p>
          <a
            target="_blank"
            rel="noopener noreferrer"
            href="https://api-platform.com/docs/"
            className="bg-white text-cyan-700 px-8 py-3 relative overflow-hidden transition-all font-extrabold text-lg group hover:pl-4 hover:pr-12"
          >
            Get started
            <div className="absolute left-full top-0 w-7 h-full bg-cyan-200 transition-all flex p-1 justify-center items-center group-hover:-translate-x-full">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
                className="w-6 h-6"
              >
                <path
                  fillRule="evenodd"
                  d="M12.97 3.97a.75.75 0 011.06 0l7.5 7.5a.75.75 0 010 1.06l-7.5 7.5a.75.75 0 11-1.06-1.06l6.22-6.22H3a.75.75 0 010-1.5h16.19l-6.22-6.22a.75.75 0 010-1.06z"
                  clipRule="evenodd"
                />
              </svg>
            </div>
          </a>
        </div>
      </div>
    </section>
    <section className="bg-white py-8">
      <div className="container | md:px-20">
        <div className="text-center | lg:text-left lg:w-3/5 lg:ml-auto">
          <h2 className="text-black text-md font-bold mb-5">
            Available services:
          </h2>
          <div className="flex justify-center flex-wrap | lg:justify-start lg:grid lg:gap-5 lg:grid-cols-2">
            <Card image={apiPicture} title="API" url="/docs" />
            <Card image={adminPicture} title="Admin" url="/admin" />
            <Card
              image={mercurePicture}
              title="Mercure debugger"
              url="/.well-known/mercure/ui/"
            />
          </div>
        </div>
      </div>
    </section>
    <div className="bg-white text-center pb-4 | md:shadow-md md:px-0.5 md:py-4 md:grid md:grid-cols-1 md:gap-3 md:fixed md:top-1/2 md:-right-1 md:-translate-y-1/2 md:portrait:bottom-4 md:portrait:top-auto md:portrait:translate-y-0">
      <h2 className="text-black text-md font-bold mb-2 | md:text-cyan-700 md:font-normal md:uppercase md:text-xs md:mx-2 md:mb-0">
        Follow us
      </h2>
      <HelpButton
        url="https://twitter.com/ApiPlatform"
        title="API Platform on Twitter"
      >
        <svg
          viewBox="0 0 20 20"
          aria-hidden="true"
          fill="currentColor"
        >
          <path d="M6.29 18.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0 0 20 3.92a8.19 8.19 0 0 1-2.357.646 4.118 4.118 0 0 0 1.804-2.27 8.224 8.224 0 0 1-2.605.996 4.107 4.107 0 0 0-6.993 3.743 11.65 11.65 0 0 1-8.457-4.287 4.106 4.106 0 0 0 1.27 5.477A4.073 4.073 0 0 1 .8 7.713v.052a4.105 4.105 0 0 0 3.292 4.022 4.095 4.095 0 0 1-1.853.07 4.108 4.108 0 0 0 3.834 2.85A8.233 8.233 0 0 1 0 16.407a11.615 11.615 0 0 0 6.29 1.84"></path>
        </svg>
      </HelpButton>
      <HelpButton
        url="https://fosstodon.org/@ApiPlatform"
        title="API Platform on Mastodon"
      >
        <svg
          fill="currentColor"
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 512 512"
        >
          <path d="M480,173.59c0-104.13-68.26-134.65-68.26-134.65C377.3,23.15,318.2,16.5,256.8,16h-1.51c-61.4.5-120.46,7.15-154.88,22.94,0,0-68.27,30.52-68.27,134.65,0,23.85-.46,52.35.29,82.59C34.91,358,51.11,458.37,145.32,483.29c43.43,11.49,80.73,13.89,110.76,12.24,54.47-3,85-19.42,85-19.42l-1.79-39.5s-38.93,12.27-82.64,10.77c-43.31-1.48-89-4.67-96-57.81a108.44,108.44,0,0,1-1-14.9,558.91,558.91,0,0,0,96.39,12.85c32.95,1.51,63.84-1.93,95.22-5.67,60.18-7.18,112.58-44.24,119.16-78.09C480.84,250.42,480,173.59,480,173.59ZM399.46,307.75h-50V185.38c0-25.8-10.86-38.89-32.58-38.89-24,0-36.06,15.53-36.06,46.24v67H231.16v-67c0-30.71-12-46.24-36.06-46.24-21.72,0-32.58,13.09-32.58,38.89V307.75h-50V181.67q0-38.65,19.75-61.39c13.6-15.15,31.4-22.92,53.51-22.92,25.58,0,44.95,9.82,57.75,29.48L256,147.69l12.45-20.85c12.81-19.66,32.17-29.48,57.75-29.48,22.11,0,39.91,7.77,53.51,22.92Q399.5,143,399.46,181.67Z" />
        </svg>
      </HelpButton>
      <HelpButton
        url="https://github.com/api-platform/api-platform"
        title="API Platform on Github"
      >
        <svg viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
          <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.013 8.013 0 0016 8c0-4.42-3.58-8-8-8z"></path>
        </svg>
      </HelpButton>
      <HelpButton url="https://api-platform.com/community" title="Need help?">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="currentColor"
        >
          <path
            fillRule="evenodd"
            d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm11.378-3.917c-.89-.777-2.366-.777-3.255 0a.75.75 0 01-.988-1.129c1.454-1.272 3.776-1.272 5.23 0 1.513 1.324 1.513 3.518 0 4.842a3.75 3.75 0 01-.837.552c-.676.328-1.028.774-1.028 1.152v.75a.75.75 0 01-1.5 0v-.75c0-1.279 1.06-2.107 1.875-2.502.182-.088.351-.199.503-.331.83-.727.83-1.857 0-2.584zM12 18a.75.75 0 100-1.5.75.75 0 000 1.5z"
            clipRule="evenodd"
          />
        </svg>
      </HelpButton>
    </div>
  </div>
);
export default Welcome;

const Card = ({
  image,
  url,
  title,
}: {
  image: string;
  url: string;
  title: string;
}) => (
  <div className="w-full max-w-xs p-2 | sm:w-1/2 | lg:w-full lg:p-0">
  <a
    href={url}
    className="w-full flex items-center flex-col justify-center shadow-card p-3 min-h-24 transition-colors text-cyan-500 border-4 border-transparent hover:border-cyan-200 hover:text-cyan-700 | sm:flex-row sm:justify-start sm:px-5"
  >
    <Image src={image} width="50" height="50" alt="" />
    <h3 className="text-center text-base uppercase font-semibold leading-tight pt-3 | sm:text-left sm:pt-0 sm:pl-5">
      {title}
    </h3>
  </a>
  </div>
);

const HelpButton = ({
  children,
  url,
  title,
}: {
  url: string;
  title: string;
  children: React.ReactNode;
}) => (
  (<Link
    href={url}
    target="_blank"
    rel="noopener noreferrer"
    className="w-12 h-12 p-2.5 rounded-full border-2 border-gray-100 justify-center transition-colors hover:border-cyan-200 hover:bg-cyan-200/50 m-2 inline-flex items-center | md:p-1 md:w-9 md:h-9 md:flex md:mx-auto md:m-0"
    title={title}>

    {children}

  </Link>)
);
