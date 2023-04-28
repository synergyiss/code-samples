import Image from "next/image";
import Link from "next/link";

export default function Home() {
  return (
    <div className="flex h-screen bg-black">
      <div className="w-screen h-screen flex flex-col justify-center items-center">
        <Image
          width={485}
          height={92}
          src="/logo.png"
          alt="Synergy Innovative Systems"
          className="w-485 h-92"
        />
        <div className="text-center max-w-screen-sm mb-10">
          <h1 className="text-stone-200 font-bold text-2xl">
            Member Intelligence
          </h1>
          <p className="text-stone-400 mt-5">
            This is a{" "}
            <Link
              href="http://synergyinnovativesystems.com/"
              target="_blank"
              rel="noopener noreferrer"
              className="text-stone-400 underline hover:text-stone-200 transition-all"
            >
              Synergy Innovative Systems
            </Link>{" "}
            development project for clubs and associations.
          </p>
        </div>
        <div className="flex space-x-3">
          <Link
            href="/protected"
            prefetch={false}
            className="text-stone-400 underline hover:text-stone-200 transition-all"
          >
            Login
          </Link>{" . "}
          <Link
            href="/dashboard"
            prefetch={false}
            className="text-stone-400 underline hover:text-stone-200 transition-all"
          >
            Dashboard
          </Link>
        </div>
      </div>
    </div>
  );
}
