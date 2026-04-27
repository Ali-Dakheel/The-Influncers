import { Target } from "lucide-react"

import { Eyebrow } from "@/components/section"
import { Reveal } from "@/components/reveal"
import { MeshBackdrop } from "@/components/mesh-backdrop"
import { NumberTicker } from "@/components/ui/number-ticker"

export function MatchingDeepDiveSection() {
  return (
    <section className="relative overflow-hidden bg-white">
      <MeshBackdrop palette="mint" />

      <div className="relative mx-auto w-full max-w-7xl px-6 py-24 lg:py-32">
        <div className="grid gap-16 lg:grid-cols-2 lg:items-center">
          {/* LEFT — copy */}
          <div>
            <Reveal>
              <Eyebrow>
                <Target className="size-3" /> Matching engine
              </Eyebrow>
            </Reveal>
            <Reveal delay={70}>
              <h2
                className="mt-6 max-w-lg text-balance text-[#0A0F1E] leading-[1.05] tracking-[-0.02em]"
                style={{
                  fontFamily: "var(--font-display)",
                  fontWeight: 400,
                  fontSize: "clamp(36px, 4.6vw, 52px)",
                }}
              >
                Match by outcome.{" "}
                <em className="text-[#2E62D9]">Not by follower count.</em>
              </h2>
            </Reveal>
            <Reveal delay={140}>
              <p className="mt-6 max-w-md text-base leading-relaxed text-[#5B6478]">
                Every match is calibrated against your actual campaign data —
                your category, your market, the creator&apos;s history. Predicted
                reach, engagement, and CPR with confidence intervals before any
                spend.
              </p>
            </Reveal>

            {/* KPI list */}
            <div className="mt-10 space-y-5">
              {[
                {
                  k: "Predicted engagement",
                  v: "8.1",
                  suffix: "%",
                  decimals: 1,
                  caption: "calibrated to creator × category × your past 90d",
                },
                {
                  k: "Fit score",
                  v: "94",
                  suffix: " / 100",
                  decimals: 0,
                  caption: "across content style, audience and brand affinity",
                },
                {
                  k: "Estimated CPR",
                  v: "0.82",
                  prefix: "$",
                  suffix: "",
                  decimals: 2,
                  caption: "with 95% confidence band ±$0.18",
                },
              ].map((kpi, i) => (
                <Reveal key={kpi.k} delay={200 + i * 60}>
                  <div className="flex items-baseline gap-5 border-t border-[#E8EAF0] pt-4">
                    <div className="flex-1">
                      <div
                        className="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#94A0B8]"
                        style={{ fontFamily: "var(--font-mono)" }}
                      >
                        {kpi.k}
                      </div>
                      <div className="mt-1 text-sm text-[#5B6478]">
                        {kpi.caption}
                      </div>
                    </div>
                    <div
                      className="flex items-baseline whitespace-nowrap text-[#0A0F1E]"
                      style={{ fontFamily: "var(--font-display)", fontWeight: 400 }}
                    >
                      {kpi.prefix && (
                        <span className="text-3xl">{kpi.prefix}</span>
                      )}
                      <NumberTicker
                        value={Number(kpi.v)}
                        decimalPlaces={kpi.decimals}
                        className="text-3xl !text-[#0A0F1E]"
                      />
                      {kpi.suffix && (
                        <span className="text-3xl">{kpi.suffix}</span>
                      )}
                    </div>
                  </div>
                </Reveal>
              ))}
            </div>
          </div>

          {/* RIGHT — data viz card */}
          <Reveal delay={140}>
            <MatchingViz />
          </Reveal>
        </div>
      </div>
    </section>
  )
}

/* ─── Custom SVG data viz ─── */

const CANDIDATES = [
  { name: "Sofia Creates", fit: 94, low: 7.2, high: 9.1, mid: 8.1 },
  { name: "Marco Visuals", fit: 88, low: 6.4, high: 8.0, mid: 7.2 },
  { name: "Lina Studio", fit: 79, low: 5.6, high: 7.4, mid: 6.5 },
  { name: "Theo Reels", fit: 72, low: 4.8, high: 6.5, mid: 5.7 },
]

function MatchingViz() {
  /* Range bar bounds: 0%..10% engagement scale */
  const SCALE_MIN = 4
  const SCALE_MAX = 10
  const pct = (v: number) => ((v - SCALE_MIN) / (SCALE_MAX - SCALE_MIN)) * 100

  return (
    <div
      className="relative rounded-3xl border border-[#0A0F1E]/8 bg-white p-6"
      style={{
        boxShadow:
          "0 32px 64px -28px rgba(10,15,30,0.14), 0 0 0 1px rgba(10,15,30,0.04), 0 4px 12px rgba(10,15,30,0.04)",
      }}
    >
      {/* Header */}
      <div className="flex items-center justify-between">
        <div>
          <div
            className="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#94A0B8]"
            style={{ fontFamily: "var(--font-mono)" }}
          >
            Brand · Adidas Sneaker Drop
          </div>
          <div className="mt-1 text-base font-semibold text-[#0A0F1E]">
            Predicted engagement by candidate
          </div>
        </div>
        <FitDonut value={94} />
      </div>

      {/* Range bars */}
      <div className="mt-6 space-y-4">
        {CANDIDATES.map((c, i) => (
          <div key={c.name}>
            <div className="mb-1.5 flex items-center justify-between">
              <span className="text-[12px] font-medium text-[#0A0F1E]">
                {c.name}
              </span>
              <span className="text-[11px] tabular-nums text-[#5B6478]">
                {c.low}–{c.high}%
              </span>
            </div>
            <div className="relative h-2 rounded-full bg-[#F6F7FB]">
              {/* Confidence band */}
              <div
                className="absolute top-0 h-full rounded-full"
                style={{
                  left: `${pct(c.low)}%`,
                  width: `${pct(c.high) - pct(c.low)}%`,
                  background:
                    i === 0
                      ? "linear-gradient(90deg, #2E62D9, #6FA3F5)"
                      : "linear-gradient(90deg, #5B6478, #94A0B8)",
                  opacity: i === 0 ? 1 : 0.5,
                }}
              />
              {/* Mid marker */}
              <div
                className="absolute top-1/2 size-3 -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-white"
                style={{
                  left: `${pct(c.mid)}%`,
                  background: i === 0 ? "#2E62D9" : "#5B6478",
                  boxShadow: "0 1px 4px rgba(10,15,30,0.18)",
                }}
              />
            </div>
          </div>
        ))}
      </div>

      {/* Scale */}
      <div className="mt-4 flex justify-between text-[10px] tabular-nums text-[#94A0B8]">
        <span>{SCALE_MIN}%</span>
        <span>6%</span>
        <span>8%</span>
        <span>{SCALE_MAX}%</span>
      </div>

      <div className="mt-6 border-t border-[#E8EAF0] pt-4">
        <div className="flex items-center justify-between text-[11px]">
          <span className="text-[#5B6478]">Confidence interval (95%)</span>
          <span className="font-semibold text-[#2E62D9]">
            Sofia Creates — recommended
          </span>
        </div>
      </div>
    </div>
  )
}

/* ─── Fit-score donut ─── */

function FitDonut({ value }: { value: number }) {
  const r = 26
  const circ = 2 * Math.PI * r
  const offset = circ * (1 - value / 100)
  return (
    <div className="relative size-[72px]">
      <svg viewBox="0 0 64 64" className="size-full -rotate-90">
        <circle
          cx="32"
          cy="32"
          r={r}
          fill="none"
          stroke="#F6F7FB"
          strokeWidth="6"
        />
        <circle
          cx="32"
          cy="32"
          r={r}
          fill="none"
          stroke="url(#donutGrad)"
          strokeWidth="6"
          strokeLinecap="round"
          strokeDasharray={circ}
          strokeDashoffset={offset}
          style={{ transition: "stroke-dashoffset 1.6s ease-out" }}
        />
        <defs>
          <linearGradient id="donutGrad" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stopColor="#2E62D9" />
            <stop offset="100%" stopColor="#6FA3F5" />
          </linearGradient>
        </defs>
      </svg>
      <div className="absolute inset-0 flex flex-col items-center justify-center">
        <span
          className="text-base text-[#0A0F1E]"
          style={{ fontFamily: "var(--font-display)", fontWeight: 400 }}
        >
          {value}
        </span>
        <span
          className="-mt-0.5 text-[8px] uppercase tracking-[0.16em] text-[#94A0B8]"
          style={{ fontFamily: "var(--font-mono)" }}
        >
          Fit
        </span>
      </div>
    </div>
  )
}
