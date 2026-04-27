import { Eyebrow } from "@/components/section"
import { Reveal } from "@/components/reveal"
import { MeshBackdrop } from "@/components/mesh-backdrop"
import { OrbitFlywheelDiagram } from "@/components/flywheel"
import { cn } from "@/lib/utils"

const FLYWHEEL_STEPS = [
  "More campaigns run",
  "Richer performance data",
  "Smarter AI matching",
  "Stronger results for brands",
  "More brands & creators join",
  "Deeper moat — impossible to replicate",
]

export function FlywheelSection() {
  return (
    <section className="relative overflow-hidden bg-white">
      <MeshBackdrop palette="sky" />

      <div className="relative mx-auto w-full max-w-7xl px-6 py-24 lg:py-32">
        <div className="grid gap-16 lg:grid-cols-2 lg:items-center">
          {/* LEFT — orbit */}
          <Reveal>
            <OrbitFlywheelDiagram />
          </Reveal>

          {/* RIGHT — copy + steps */}
          <div>
            <Reveal>
              <Eyebrow>The flywheel</Eyebrow>
            </Reveal>
            <Reveal delay={70}>
              <h2
                className="mt-6 max-w-md text-balance text-[#0A0F1E] leading-[1.05] tracking-[-0.02em]"
                style={{
                  fontFamily: "var(--font-display)",
                  fontWeight: 400,
                  fontSize: "clamp(36px, 4.6vw, 52px)",
                }}
              >
                The more campaigns run,{" "}
                <em className="text-[#2E62D9]">the smarter it gets.</em>
              </h2>
            </Reveal>
            <Reveal delay={140}>
              <p className="mt-6 max-w-md text-base leading-relaxed text-[#5B6478]">
                Every outcome tightens the matching, sharpens the predictions,
                deepens the moat. The system that runs more campaigns wins —
                permanently.
              </p>
            </Reveal>

            {/* steps */}
            <ol className="mt-10 space-y-4">
              {FLYWHEEL_STEPS.map((step, i) => {
                const isLast = i === FLYWHEEL_STEPS.length - 1
                return (
                  <Reveal key={step} delay={180 + i * 60} as="li">
                    <div className="flex items-center gap-4">
                      <div
                        className={cn(
                          "flex size-9 flex-shrink-0 items-center justify-center rounded-full text-[12px] font-bold",
                          isLast
                            ? "bg-[#2E62D9] text-white shadow-[0_0_24px_rgba(46,98,217,0.35)]"
                            : "border border-[#2E62D9]/25 bg-white text-[#2E62D9]"
                        )}
                        style={{ fontFamily: "var(--font-mono)" }}
                      >
                        {i + 1}
                      </div>
                      <p
                        className={cn(
                          "text-[15px]",
                          isLast
                            ? "font-semibold text-[#2E62D9]"
                            : "font-medium text-[#0A0F1E]"
                        )}
                      >
                        {step}
                      </p>
                    </div>
                  </Reveal>
                )
              })}
            </ol>
          </div>
        </div>
      </div>
    </section>
  )
}
