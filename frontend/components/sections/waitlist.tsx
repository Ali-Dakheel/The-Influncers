import { ArrowRight, Check } from "lucide-react"

import { Eyebrow } from "@/components/section"
import { Reveal } from "@/components/reveal"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"

const REASSURANCES = [
  "Joining waitlist · no commitment",
  "Onboarded in cohorts of 10",
  "Built with you · before launch",
]

export function WaitlistSection() {
  return (
    <section id="pricing" className="relative bg-white">
      <div className="mx-auto w-full max-w-5xl px-6 py-24 lg:py-32">
        <Reveal>
          <div
            className="relative overflow-hidden rounded-3xl border border-[#E8EAF0] bg-white px-6 py-12 sm:px-12 sm:py-14 lg:px-16"
            style={{
              boxShadow:
                "0 0 0 1px rgba(10,15,30,0.02), 0 24px 64px -24px rgba(10,15,30,0.10), 0 4px 16px -8px rgba(10,15,30,0.04)",
            }}
          >
            {/* Single subtle accent ring at top */}
            <div
              aria-hidden
              className="pointer-events-none absolute inset-x-0 top-0 h-px"
              style={{
                background:
                  "linear-gradient(90deg, transparent, rgba(46,98,217,0.35), transparent)",
              }}
            />

            <div className="grid gap-10 lg:grid-cols-[1.4fr_1fr] lg:items-center">
              <div>
                <Eyebrow>Get access</Eyebrow>
                <h2
                  className="mt-5 max-w-md text-balance text-[#0A0F1E] leading-[1.05] tracking-[-0.02em]"
                  style={{
                    fontFamily: "var(--font-display)",
                    fontWeight: 400,
                    fontSize: "clamp(32px, 4.4vw, 48px)",
                  }}
                >
                  Built with select partners.{" "}
                  <em className="text-[#2E62D9]">Joining waitlist now.</em>
                </h2>
                <p className="mt-5 max-w-sm text-base leading-relaxed text-[#5B6478]">
                  We&apos;re onboarding agencies and creator networks in cohorts
                  of ten. Each cohort shapes the platform before the next ships.
                </p>

                <ul className="mt-6 space-y-2">
                  {REASSURANCES.map((r) => (
                    <li
                      key={r}
                      className="flex items-center gap-2 text-sm text-[#5B6478]"
                    >
                      <Check className="size-3.5 text-[#10B981]" /> {r}
                    </li>
                  ))}
                </ul>
              </div>

              {/* Form */}
              <div className="rounded-2xl border border-[#E8EAF0] bg-[#FBFBFC] p-6">
                <div
                  className="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#94A0B8]"
                  style={{ fontFamily: "var(--font-mono)" }}
                >
                  Request access
                </div>
                <form className="mt-3 space-y-3">
                  <Input
                    placeholder="you@yourbrand.com"
                    type="email"
                    className="h-11 border-[#E8EAF0] bg-white"
                  />
                  <Input
                    placeholder="Company"
                    className="h-11 border-[#E8EAF0] bg-white"
                  />
                  <Button
                    size="lg"
                    className="h-11 w-full justify-center gap-2 bg-[#0A0F1E] text-white hover:bg-[#1A2236]"
                  >
                    Join waitlist
                    <ArrowRight className="size-4" />
                  </Button>
                </form>
                <p className="mt-3 text-[11px] leading-relaxed text-[#94A0B8]">
                  We respond within 24h. No spam. No drip sequences.
                </p>
              </div>
            </div>
          </div>
        </Reveal>
      </div>
    </section>
  )
}
