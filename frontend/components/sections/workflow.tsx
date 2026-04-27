import {
  CheckCircle2,
  CreditCard,
  FileText,
  Sparkles,
  Target,
  type LucideIcon,
} from "lucide-react"
import { type ReactNode } from "react"

import { Eyebrow } from "@/components/section"
import { Reveal } from "@/components/reveal"

type Step = {
  n: string
  Icon: LucideIcon
  title: string
  body: string
  accent: string
  Preview: () => ReactNode
}

const STEPS: Step[] = [
  {
    n: "01",
    Icon: FileText,
    title: "Brief",
    body: "A brand opens a campaign in two minutes — goals, budget, audience, content guidelines.",
    accent: "#2E62D9",
    Preview: BriefPreview,
  },
  {
    n: "02",
    Icon: Target,
    title: "Match",
    body: "AI ranks every fitting creator by outcome data. Predicted reach and CPR appear before any spend.",
    accent: "#7C3AED",
    Preview: MatchPreview,
  },
  {
    n: "03",
    Icon: Sparkles,
    title: "Review",
    body: "Drafts arrive. AI runs five checks in 60 seconds. Issues caught long before any human eye.",
    accent: "#047857",
    Preview: ReviewPreview,
  },
  {
    n: "04",
    Icon: CreditCard,
    title: "Pay",
    body: "Stripe Connect dual-direction. Brands pay, creators get paid, multi-currency, automated.",
    accent: "#A16207",
    Preview: PayPreview,
  },
]

export function WorkflowSection() {
  return (
    <section id="workflow" className="relative bg-white">
      <div className="mx-auto w-full max-w-7xl px-6 py-24 lg:py-32">
        <div className="mx-auto max-w-3xl text-center">
          <Reveal>
            <Eyebrow>Workflow</Eyebrow>
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
              Four steps.{" "}
              <em className="text-[#2E62D9]">
                Brief, Match, Review, Pay.
              </em>
            </h2>
          </Reveal>
          <Reveal delay={140}>
            <p className="mx-auto mt-6 max-w-xl text-base leading-relaxed text-[#5B6478]">
              The platform handles every step end-to-end. The agent makes
              decisions inside each step. Humans only step in when judgment
              matters.
            </p>
          </Reveal>
        </div>

        <div className="relative mt-16">
          <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            {STEPS.map((step, i) => (
              <Reveal key={step.n} delay={i * 80}>
                <StepCard step={step} />
              </Reveal>
            ))}
          </div>
        </div>
      </div>
    </section>
  )
}

function StepCard({ step }: { step: Step }) {
  const { Icon, Preview } = step
  return (
    <div
      className="relative h-full rounded-3xl border border-[#E8EAF0] bg-white p-6"
      style={{
        boxShadow:
          "0 0 0 1px rgba(10,15,30,0.02), 0 1px 2px rgba(10,15,30,0.04), 0 8px 24px -16px rgba(10,15,30,0.06)",
      }}
    >
      <div className="flex items-start justify-between">
        <div
          className="flex size-10 items-center justify-center rounded-xl"
          style={{
            background: `${step.accent}10`,
            border: `1px solid ${step.accent}25`,
          }}
        >
          <Icon className="size-4" style={{ color: step.accent }} />
        </div>
        <span
          className="text-xs font-semibold uppercase tracking-[0.16em] text-[#94A0B8]"
          style={{ fontFamily: "var(--font-mono)" }}
        >
          {step.n}
        </span>
      </div>

      <h3
        className="mt-5 text-[#0A0F1E]"
        style={{
          fontFamily: "var(--font-display)",
          fontWeight: 400,
          fontSize: "26px",
        }}
      >
        {step.title}
      </h3>
      <p className="mt-2 text-sm leading-relaxed text-[#5B6478]">{step.body}</p>

      <div className="mt-5 border-t border-[#E8EAF0] pt-4">
        <Preview />
      </div>
    </div>
  )
}

/* ─── Tiny inline mockups ─── */

function BriefPreview() {
  return (
    <div className="space-y-1.5">
      {[
        { l: "Goal", v: "Brand awareness" },
        { l: "Budget", v: "$80,000" },
        { l: "Markets", v: "SE · UK · DE" },
      ].map((row) => (
        <div
          key={row.l}
          className="flex items-center justify-between text-[11px]"
        >
          <span className="text-[#94A0B8]">{row.l}</span>
          <span className="font-semibold text-[#0A0F1E]">{row.v}</span>
        </div>
      ))}
    </div>
  )
}

function MatchPreview() {
  return (
    <div className="space-y-1.5">
      {[
        { name: "Sofia Creates", v: "94" },
        { name: "Marco Visuals", v: "88" },
        { name: "Lina Studio", v: "79" },
      ].map((c) => (
        <div
          key={c.name}
          className="flex items-center justify-between text-[11px]"
        >
          <span className="font-medium text-[#0A0F1E]">{c.name}</span>
          <span className="font-semibold text-[#7C3AED]">{c.v}/100</span>
        </div>
      ))}
    </div>
  )
}

function ReviewPreview() {
  return (
    <div className="space-y-1.5">
      {["Frame", "Audio", "Brief match", "Discount code"].map((label) => (
        <div key={label} className="flex items-center gap-2 text-[11px]">
          <CheckCircle2 className="size-3 text-[#10B981]" />
          <span className="text-[#5B6478]">{label}</span>
        </div>
      ))}
    </div>
  )
}

function PayPreview() {
  return (
    <div>
      <div className="flex items-baseline justify-between">
        <span
          className="text-[10px] font-semibold uppercase tracking-[0.16em] text-[#94A0B8]"
          style={{ fontFamily: "var(--font-mono)" }}
        >
          Payout
        </span>
        <span
          className="text-lg text-[#0A0F1E]"
          style={{ fontFamily: "var(--font-display)", fontWeight: 400 }}
        >
          $4,283
        </span>
      </div>
      <div className="mt-2 flex items-center justify-between text-[10px]">
        <span className="text-[#94A0B8]">Settled</span>
        <span className="font-semibold text-[#10B981]">Today, 9:41 AM</span>
      </div>
    </div>
  )
}
