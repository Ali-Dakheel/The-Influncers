import { Fragment } from "react"
import { Check, X } from "lucide-react"

import { Eyebrow } from "@/components/section"
import { Reveal } from "@/components/reveal"

const COMPARISON: { old: string; next: string }[] = [
  { old: "Manual outreach to every creator", next: "AI-surfaced fits in seconds" },
  { old: "Follower-count matching", next: "Outcome-data scoring per market" },
  { old: "Weeks-long human draft reviews", next: "60-second AI draft reviewer" },
  { old: "Country-by-country chaos", next: "Multi-market by default" },
  { old: "Crisis = email everyone", next: "One-click campaign halt" },
  { old: "Reports built after the fact", next: "Live mid-campaign intelligence" },
]

export function OldVsNewSection() {
  return (
    <section className="relative bg-white">
      <div className="mx-auto w-full max-w-7xl px-6 py-24 lg:py-32">
        {/* Heading */}
        <div className="mx-auto max-w-3xl text-center">
          <Reveal>
            <Eyebrow>The shift</Eyebrow>
          </Reveal>
          <Reveal delay={70}>
            <h2
              className="mt-6 text-balance text-[#0A0F1E] leading-[1.05] tracking-[-0.02em]"
              style={{
                fontFamily: "var(--font-display)",
                fontWeight: 400,
                fontSize: "clamp(36px, 5vw, 56px)",
              }}
            >
              Manual is dying.{" "}
              <em className="text-[#2E62D9]">Agentic is here.</em>
            </h2>
          </Reveal>
          <Reveal delay={140}>
            <p className="mx-auto mt-6 max-w-xl text-base leading-relaxed text-[#5B6478]">
              The legacy stack was built before AI. It manages databases, not
              relationships. The new system is the opposite — autonomous,
              outcome-led, calibrated by every campaign that runs through it.
            </p>
          </Reveal>
        </div>

        {/* Comparison grid */}
        <div className="mt-16 grid gap-px overflow-hidden rounded-3xl border border-[#E8EAF0] bg-[#E8EAF0] sm:grid-cols-2">
          {/* Header row */}
          <div className="flex items-center gap-3 bg-[#FBFBFC] px-6 py-5">
            <div
              className="flex size-7 items-center justify-center rounded-full bg-[#0A0F1E]/6"
              aria-hidden
            >
              <X className="size-3.5 text-[#94A0B8]" />
            </div>
            <div>
              <div
                className="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#94A0B8]"
                style={{ fontFamily: "var(--font-mono)" }}
              >
                Today
              </div>
              <div className="mt-0.5 text-sm font-semibold text-[#5B6478]">
                The legacy way
              </div>
            </div>
          </div>
          <div className="flex items-center gap-3 bg-white px-6 py-5">
            <div
              className="flex size-7 items-center justify-center rounded-full bg-[#2E62D9]/10"
              aria-hidden
            >
              <Check className="size-3.5 text-[#2E62D9]" />
            </div>
            <div>
              <div
                className="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#2E62D9]"
                style={{ fontFamily: "var(--font-mono)" }}
              >
                With The Influncers
              </div>
              <div className="mt-0.5 text-sm font-semibold text-[#0A0F1E]">
                The agentic way
              </div>
            </div>
          </div>

          {/* Comparison rows — interleaved (old then new) so grid lays them out
              side-by-side row-by-row */}
          {COMPARISON.map((row, i) => (
            <Fragment key={row.old}>
              <Reveal delay={i * 50}>
                <div className="h-full bg-[#FBFBFC] px-6 py-5">
                  <div className="flex items-center gap-3">
                    <span
                      className="size-1 flex-shrink-0 rounded-full bg-[#94A0B8]"
                      aria-hidden
                    />
                    <span className="text-[15px] text-[#94A0B8] line-through decoration-[#94A0B8]/30 decoration-[1px]">
                      {row.old}
                    </span>
                  </div>
                </div>
              </Reveal>
              <Reveal delay={i * 50 + 30}>
                <div className="h-full bg-white px-6 py-5">
                  <div className="flex items-center gap-3">
                    <Check
                      className="size-3.5 flex-shrink-0 text-[#2E62D9]"
                      aria-hidden
                    />
                    <span className="text-[15px] font-medium text-[#0A0F1E]">
                      {row.next}
                    </span>
                  </div>
                </div>
              </Reveal>
            </Fragment>
          ))}
        </div>
      </div>
    </section>
  )
}
