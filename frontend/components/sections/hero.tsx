import { ArrowRight, Sparkles } from "lucide-react"

import { Button } from "@/components/ui/button"
import { AnimatedShinyText } from "@/components/ui/animated-shiny-text"
import { NumberTicker } from "@/components/ui/number-ticker"
import { Reveal } from "@/components/reveal"
import { MeshBackdrop } from "@/components/mesh-backdrop"
import { AgentTimelineDevice } from "@/components/agent-timeline-device"

export function HeroSection() {
  return (
    <section className="relative overflow-hidden pt-24">
      <MeshBackdrop palette="hero" />

      <div className="relative mx-auto w-full max-w-7xl px-6 pb-24 pt-20 lg:pb-32 lg:pt-28">
        <div className="grid gap-16 lg:grid-cols-[1.05fr_1fr] lg:items-center">
          {/* LEFT */}
          <div>
            <Reveal>
              <div className="inline-flex items-center gap-2 rounded-full border border-[#0A0F1E]/8 bg-white/70 px-3 py-1.5 backdrop-blur-sm">
                <Sparkles className="size-3 text-[#2E62D9]" />
                <AnimatedShinyText
                  className="!mx-0 text-[11px] font-semibold uppercase !tracking-[0.18em] text-[#5B6478]"
                  shimmerWidth={80}
                >
                  AI‑Native · Influencer Marketing
                </AnimatedShinyText>
              </div>
            </Reveal>

            <Reveal delay={70}>
              <h1
                className="mt-7 max-w-[680px] text-balance leading-[1.02] tracking-[-0.025em] text-[#0A0F1E]"
                style={{
                  fontFamily: "var(--font-display)",
                  fontWeight: 400,
                  fontSize: "clamp(48px, 7.2vw, 86px)",
                }}
              >
                Influencer marketing,{" "}
                <em className="text-[#2E62D9]">rebuilt around the agent.</em>
              </h1>
            </Reveal>

            <Reveal delay={140}>
              <p className="mt-6 max-w-[480px] text-base leading-relaxed text-[#5B6478] sm:text-lg">
                Every campaign sharpens the platform. The matching tightens, the
                predictions calibrate, the data moat compounds — without a
                competitor in sight.
              </p>
            </Reveal>

            <Reveal delay={200}>
              <div className="mt-9 flex flex-wrap items-center gap-3">
                <Button
                  size="lg"
                  className="h-11 gap-2 bg-[#0A0F1E] px-5 text-white hover:bg-[#1A2236]"
                >
                  Request access <ArrowRight className="size-4" />
                </Button>
                <Button
                  size="lg"
                  variant="outline"
                  className="h-11 border-[#0A0F1E]/10 bg-white/70 px-5 text-[#0A0F1E] backdrop-blur-sm hover:bg-white"
                >
                  See how it works
                </Button>
              </div>
            </Reveal>

            <Reveal delay={260}>
              <div className="mt-14 flex items-center gap-7 sm:gap-10">
                <Kpi value={500} suffix="" label="campaigns / 1 person" />
                <Divider />
                <Kpi value={60} suffix="s" label="AI draft review" />
                <Divider />
                <Kpi prefix="$" value={70} suffix="B" label="market by 2032" />
              </div>
            </Reveal>
          </div>

          {/* RIGHT — Agent timeline device */}
          <Reveal delay={120} className="relative">
            <AgentTimelineDevice />
          </Reveal>
        </div>
      </div>

      {/* Bottom hairline fade */}
      <div className="pointer-events-none absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-[#0A0F1E]/12 to-transparent" />
    </section>
  )
}

function Kpi({
  value,
  prefix,
  suffix,
  label,
}: {
  value: number
  prefix?: string
  suffix?: string
  label: string
}) {
  return (
    <div>
      <div
        className="flex items-baseline text-[#0A0F1E]"
        style={{ fontFamily: "var(--font-display)" }}
      >
        {prefix && (
          <span className="text-2xl font-light leading-none">{prefix}</span>
        )}
        <NumberTicker
          value={value}
          className="text-2xl font-light leading-none tracking-tight !text-[#0A0F1E]"
        />
        {suffix && (
          <span className="text-2xl font-light leading-none">{suffix}</span>
        )}
      </div>
      <div className="mt-1.5 text-xs text-[#94A0B8]">{label}</div>
    </div>
  )
}

function Divider() {
  return <div className="h-8 w-px bg-[#0A0F1E]/10" />
}
