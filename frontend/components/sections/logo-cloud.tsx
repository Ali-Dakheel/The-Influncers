import { Marquee } from "@/components/ui/marquee"
import { Reveal } from "@/components/reveal"

const WORDMARKS = [
  "RAINCODE",
  "Söhne Studio",
  "FORGE×",
  "CLAY",
  "Helsinki Group",
  "AKVA",
  "Northbeam",
  "Stockholm Co.",
  "Bahrain Atelier",
  "ORBIT",
  "Gradient",
  "MILA",
]

/**
 * Logo cloud — single greyscale Marquee with brand wordmarks.
 * Replaces the old "ProblemStrip" red-icon marquee.
 */
export function LogoCloudSection() {
  return (
    <section className="relative border-y border-[#E8EAF0] bg-[#FBFBFC]">
      <div className="mx-auto flex w-full max-w-7xl flex-col items-center px-6 py-12">
        <Reveal>
          <p
            className="text-[11px] font-semibold uppercase tracking-[0.22em] text-[#94A0B8]"
            style={{ fontFamily: "var(--font-mono)" }}
          >
            Built for the next era of brand × creator
          </p>
        </Reveal>

        <Reveal delay={120} className="mt-8 w-full">
          <div className="relative">
            {/* Edge fade overlays */}
            <div className="pointer-events-none absolute inset-y-0 left-0 z-10 w-24 bg-gradient-to-r from-[#FBFBFC] to-transparent" />
            <div className="pointer-events-none absolute inset-y-0 right-0 z-10 w-24 bg-gradient-to-l from-[#FBFBFC] to-transparent" />

            <Marquee className="[--duration:50s] [--gap:3.5rem]" pauseOnHover>
              {WORDMARKS.map((mark) => (
                <span
                  key={mark}
                  className="select-none whitespace-nowrap text-2xl font-semibold tracking-tight text-[#94A0B8]/80 transition-colors hover:text-[#0A0F1E]"
                  style={{ fontFamily: "var(--font-display)", fontWeight: 400 }}
                >
                  {mark}
                </span>
              ))}
            </Marquee>
          </div>
        </Reveal>
      </div>
    </section>
  )
}
