import { Eyebrow } from "@/components/section"
import { Reveal } from "@/components/reveal"
import { NumberTicker } from "@/components/ui/number-ticker"

const STATS = [
  {
    eyebrow: "The KPI",
    prefix: "",
    value: 500,
    suffix: "",
    color: "#2E62D9",
    label:
      "campaigns run by one person, simultaneously — the number that defines this platform.",
  },
  {
    eyebrow: "Market by 2032",
    prefix: "$",
    value: 70,
    suffix: "B",
    color: "#0A0F1E",
    label:
      "a global market doubling in size. We are entering at exactly the right moment with the right architecture.",
  },
  {
    eyebrow: "AI-native competitors",
    prefix: "",
    value: 0,
    suffix: "",
    color: "#94A0B8",
    label:
      "platforms built AI-native from scratch. Everyone else bolted AI onto a legacy stack — and it shows.",
  },
]

export function StatsSection() {
  return (
    <section className="relative bg-[#FBFBFC]">
      <div className="mx-auto w-full max-w-7xl px-6 py-24 lg:py-32">
        <div className="mx-auto max-w-3xl text-center">
          <Reveal>
            <Eyebrow>The numbers</Eyebrow>
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
              Three numbers{" "}
              <em className="text-[#2E62D9]">that tell the story.</em>
            </h2>
          </Reveal>
        </div>

        <div className="mt-20 grid grid-cols-1 gap-px overflow-hidden rounded-3xl border border-[#E8EAF0] bg-[#E8EAF0] md:grid-cols-3">
          {STATS.map((s, i) => (
            <Reveal key={i} delay={i * 110}>
              <div className="flex h-full flex-col bg-white px-8 py-12 sm:px-10 sm:py-14">
                <div
                  className="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#94A0B8]"
                  style={{ fontFamily: "var(--font-mono)" }}
                >
                  {s.eyebrow}
                </div>

                <div
                  className="mt-3 flex items-baseline leading-none"
                  style={{ fontFamily: "var(--font-display)", fontWeight: 400, color: s.color }}
                >
                  {s.prefix && (
                    <span className="text-7xl sm:text-8xl">{s.prefix}</span>
                  )}
                  {s.value === 0 ? (
                    <span className="text-7xl sm:text-8xl">0</span>
                  ) : (
                    <NumberTicker
                      value={s.value}
                      className="text-7xl sm:text-8xl"
                      style={{ color: s.color }}
                    />
                  )}
                  {s.suffix && (
                    <span className="text-7xl sm:text-8xl">{s.suffix}</span>
                  )}
                </div>

                <div className="mt-6 h-px w-10" style={{ background: s.color, opacity: 0.3 }} />

                <p className="mt-5 max-w-[260px] text-sm leading-relaxed text-[#5B6478]">
                  {s.label}
                </p>
              </div>
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  )
}
