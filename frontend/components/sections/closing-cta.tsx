import { ArrowRight } from "lucide-react"

import { Reveal } from "@/components/reveal"
import { MeshBackdrop } from "@/components/mesh-backdrop"
import { Button } from "@/components/ui/button"

export function ClosingCtaSection() {
  return (
    <section className="relative overflow-hidden bg-white">
      <MeshBackdrop palette="cta" />

      <div className="relative mx-auto w-full max-w-5xl px-6 py-32 text-center">
        <Reveal>
          <span
            className="inline-flex items-center gap-2 rounded-full border border-[#0A0F1E]/8 bg-white/70 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-[#5B6478] backdrop-blur-sm"
            style={{ fontFamily: "var(--font-mono)" }}
          >
            500 — the goal
          </span>
        </Reveal>

        <Reveal delay={70}>
          <h2
            className="mx-auto mt-7 max-w-3xl text-balance text-[#0A0F1E] leading-[1] tracking-[-0.025em]"
            style={{
              fontFamily: "var(--font-display)",
              fontWeight: 400,
              fontSize: "clamp(48px, 7vw, 96px)",
            }}
          >
            One person.{" "}
            <em className="text-[#2E62D9]">500 campaigns.</em>{" "}
            Simultaneously.
          </h2>
        </Reveal>

        <Reveal delay={140}>
          <p className="mx-auto mt-7 max-w-xl text-base leading-relaxed text-[#5B6478] sm:text-lg">
            AI handling everything that does not require a human decision. That
            is the goal. That is what we are building.
          </p>
        </Reveal>

        <Reveal delay={200}>
          <div className="mt-10 flex flex-wrap items-center justify-center gap-3">
            <Button
              size="lg"
              className="h-12 gap-2 bg-[#0A0F1E] px-7 text-white hover:bg-[#1A2236]"
            >
              Request access
              <ArrowRight className="size-4" />
            </Button>
            <Button
              size="lg"
              variant="outline"
              className="h-12 border-[#0A0F1E]/10 bg-white/70 px-7 text-[#0A0F1E] backdrop-blur-sm hover:bg-white"
            >
              Talk to the team
            </Button>
          </div>
        </Reveal>
      </div>
    </section>
  )
}
