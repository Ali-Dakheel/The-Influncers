import {
  Bot,
  CheckCircle2,
  CreditCard,
  Smartphone,
  Store,
  Target,
  TrendingUp,
} from "lucide-react"
import { ReactNode } from "react"

import { Eyebrow } from "@/components/section"
import { Reveal } from "@/components/reveal"
import { cn } from "@/lib/utils"

type BentoCardData = {
  Icon: typeof Store
  phase: string
  title: string
  body: string
  /** col-span on lg breakpoint, 1 or 2 */
  span: 1 | 2
  /** accent ink colour for icon, phase chip and corner glow */
  accent: string
  preview?: ReactNode
}

/* ─── inline product previews ─── */

const MarketplacePreview = () => (
  <div className="space-y-2">
    {[
      { name: "Adidas — Sneaker Drop", status: "Live", color: "#10B981" },
      { name: "Nike × Sofia", status: "Matching", color: "#2E62D9" },
      { name: "Lululemon — Studio", status: "Draft", color: "#F59E0B" },
    ].map((row) => (
      <div
        key={row.name}
        className="flex items-center gap-3 rounded-lg border border-[#E8EAF0] bg-[#FBFBFC] px-3 py-2"
      >
        <div
          className="size-7 flex-shrink-0 rounded-md bg-gradient-to-br from-[#2E62D9]/15 to-[#2E62D9]/5 text-[11px] font-bold leading-none text-[#2E62D9] flex items-center justify-center"
        >
          {row.name[0]}
        </div>
        <span className="flex-1 truncate text-[12px] font-medium text-[#0A0F1E]">
          {row.name}
        </span>
        <span
          className="rounded-full px-2 py-0.5 text-[10px] font-semibold"
          style={{ backgroundColor: `${row.color}15`, color: row.color }}
        >
          {row.status}
        </span>
      </div>
    ))}
  </div>
)

const ReviewerPreview = () => (
  <div className="rounded-lg border border-[#E8EAF0] bg-[#FBFBFC] p-3">
    <div className="space-y-1.5">
      {[
        { label: "Frame check", ok: true },
        { label: "Audio quality", ok: true },
        { label: "Brief match", ok: true },
        { label: "Discount code", ok: true },
      ].map((item) => (
        <div key={item.label} className="flex items-center gap-2">
          <CheckCircle2 className="size-3.5 text-[#10B981]" />
          <span className="text-[11px] text-[#5B6478]">{item.label}</span>
        </div>
      ))}
    </div>
    <div className="mt-3 flex items-center justify-between border-t border-[#0A0F1E]/6 pt-2.5">
      <span
        className="text-[10px] font-semibold uppercase tracking-[0.16em] text-[#94A0B8]"
        style={{ fontFamily: "var(--font-mono)" }}
      >
        Verdict
      </span>
      <span className="rounded-full bg-[#10B981]/12 px-2 py-0.5 text-[10px] font-semibold text-[#10B981]">
        Approved · 42s
      </span>
    </div>
  </div>
)

const CreatorOsPreview = () => (
  <div className="grid grid-cols-3 gap-2">
    {[
      { l: "This month", v: "$28k" },
      { l: "Avg eng", v: "8.1%" },
      { l: "Active", v: "4" },
    ].map((s) => (
      <div
        key={s.l}
        className="rounded-lg border border-[#E8EAF0] bg-[#FBFBFC] px-2 py-2 text-center"
      >
        <div
          className="text-base text-[#0A0F1E]"
          style={{ fontFamily: "var(--font-display)", fontWeight: 400 }}
        >
          {s.v}
        </div>
        <div className="mt-0.5 text-[9px] uppercase tracking-[0.12em] text-[#94A0B8]">
          {s.l}
        </div>
      </div>
    ))}
  </div>
)

const MatchingPreview = () => (
  <div className="grid grid-cols-3 gap-2">
    {[
      { l: "Fit", v: "94", suf: "/100", color: "#2E62D9" },
      { l: "Eng", v: "8.1", suf: "%", color: "#10B981" },
      { l: "CPR", v: "$0.82", suf: "", color: "#F59E0B" },
    ].map((s) => (
      <div
        key={s.l}
        className="rounded-lg border border-[#E8EAF0] bg-[#FBFBFC] p-3 text-center"
      >
        <div
          className="text-xl leading-none"
          style={{ fontFamily: "var(--font-display)", fontWeight: 400, color: s.color }}
        >
          {s.v}
          <span className="text-[10px] opacity-60">{s.suf}</span>
        </div>
        <div className="mt-2 text-[10px] uppercase tracking-[0.12em] text-[#94A0B8]">
          {s.l}
        </div>
      </div>
    ))}
  </div>
)

const PaymentsPreview = () => (
  <div className="rounded-lg border border-[#E8EAF0] bg-[#FBFBFC] p-3">
    <div className="flex items-center justify-between">
      <div>
        <div
          className="text-[10px] font-semibold uppercase tracking-[0.16em] text-[#94A0B8]"
          style={{ fontFamily: "var(--font-mono)" }}
        >
          Payout · Today
        </div>
        <div
          className="mt-1 text-xl text-[#0A0F1E]"
          style={{ fontFamily: "var(--font-display)", fontWeight: 400 }}
        >
          $4,283.00
        </div>
      </div>
      <span className="rounded-full bg-[#10B981]/12 px-2.5 py-1 text-[10px] font-semibold text-[#10B981]">
        Settled
      </span>
    </div>
    <div className="mt-3 border-t border-[#0A0F1E]/6 pt-2 text-[11px] text-[#5B6478]">
      Adidas — Sneaker Drop · ref. INV-2026-0412
    </div>
  </div>
)

const PaidAdsPreview = () => {
  const bars = [42, 60, 78, 56, 84, 70, 92]
  return (
    <div className="rounded-lg border border-[#E8EAF0] bg-[#FBFBFC] p-3">
      <div className="mb-2 flex items-center justify-between">
        <span
          className="text-[10px] font-semibold uppercase tracking-[0.16em] text-[#94A0B8]"
          style={{ fontFamily: "var(--font-mono)" }}
        >
          Boost performance · 7d
        </span>
        <span className="text-[10px] font-semibold text-[#10B981]">+34%</span>
      </div>
      <div className="flex h-12 items-end gap-1">
        {bars.map((h, i) => (
          <div
            key={i}
            className="flex-1 rounded-sm bg-gradient-to-t from-[#2E62D9] to-[#6FA3F5]"
            style={{ height: `${h}%`, opacity: 0.4 + (h / 100) * 0.6 }}
          />
        ))}
      </div>
    </div>
  )
}

/* ─── Card data ─── */

const CARDS: BentoCardData[] = [
  {
    Icon: Store,
    phase: "Phase 1",
    title: "Campaign Marketplace",
    body: "Brands post briefs in minutes. AI surfaces the right creators before a human even looks.",
    span: 2,
    accent: "#C2410C",
    preview: <MarketplacePreview />,
  },
  {
    Icon: Bot,
    phase: "POC",
    title: "AI Draft Reviewer",
    body: "Every draft reviewed in 60 seconds. Wrong environment, brief mismatch — caught before any human sees it.",
    span: 1,
    accent: "#7C3AED",
    preview: <ReviewerPreview />,
  },
  {
    Icon: Smartphone,
    phase: "Phase 1",
    title: "Creator OS",
    body: "Income, performance, pricing, AI coaching — the creator's whole business in one app.",
    span: 1,
    accent: "#047857",
    preview: <CreatorOsPreview />,
  },
  {
    Icon: Target,
    phase: "Phase 2A",
    title: "Matching Engine",
    body: "Outcome-data scoring per creator × category × market. Predicted reach with confidence intervals before any spend.",
    span: 2,
    accent: "#1E4FC7",
    preview: <MatchingPreview />,
  },
  {
    Icon: CreditCard,
    phase: "Phase 1",
    title: "Payments & Invoicing",
    body: "Stripe Connect dual-direction payouts. Multi-currency. Automated invoicing both sides.",
    span: 1,
    accent: "#A16207",
    preview: <PaymentsPreview />,
  },
  {
    Icon: TrendingUp,
    phase: "Phase 2B",
    title: "Paid Ads Boosting",
    body: "Top organic content boosted to Meta and TikTok Ads — directly from the platform. AI recommends what to boost.",
    span: 2,
    accent: "#BE185D",
    preview: <PaidAdsPreview />,
  },
]

export function BentoSection() {
  return (
    <section id="platform" className="relative bg-white">
      <div className="mx-auto w-full max-w-7xl px-6 py-24 lg:py-32">
        {/* Heading */}
        <div className="mx-auto max-w-3xl text-center">
          <Reveal>
            <Eyebrow>The platform</Eyebrow>
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
              One platform.{" "}
              <em className="text-[#2E62D9]">Six modules.</em>
            </h2>
          </Reveal>
          <Reveal delay={140}>
            <p className="mx-auto mt-6 max-w-xl text-base leading-relaxed text-[#5B6478]">
              Every module shares one data model. Every campaign sharpens every
              other prediction. The system compounds, end to end.
            </p>
          </Reveal>
        </div>

        {/* Bento grid */}
        <div className="mt-16 grid grid-cols-1 gap-5 lg:grid-cols-3">
          {CARDS.map((c, i) => (
            <Reveal
              key={c.title}
              delay={i * 60}
              className={cn(c.span === 2 ? "lg:col-span-2" : "lg:col-span-1")}
            >
              <BentoCard card={c} />
            </Reveal>
          ))}
        </div>
      </div>
    </section>
  )
}

function BentoCard({ card }: { card: BentoCardData }) {
  const { Icon } = card
  return (
    <div
      className="group relative h-full overflow-hidden rounded-3xl border border-[#E8EAF0] bg-white transition-all duration-300 hover:-translate-y-0.5 hover:border-[#0A0F1E]/12"
      style={{
        boxShadow:
          "0 0 0 1px rgba(10,15,30,0.02), 0 1px 2px rgba(10,15,30,0.04), 0 8px 24px -16px rgba(10,15,30,0.06)",
      }}
    >
      {/* Subtle accent corner — barely there */}
      <div
        aria-hidden
        className="pointer-events-none absolute -right-24 -top-24 size-56 rounded-full opacity-[0.10] blur-3xl transition-opacity duration-300 group-hover:opacity-[0.18]"
        style={{ background: card.accent }}
      />
      {/* Hairline accent on top edge */}
      <div
        aria-hidden
        className="pointer-events-none absolute inset-x-0 top-0 h-px"
        style={{
          background: `linear-gradient(90deg, transparent, ${card.accent}40, transparent)`,
        }}
      />

      <div className="relative flex h-full flex-col gap-6 p-7">
        <div className="flex items-start justify-between">
          <div
            className="flex size-11 items-center justify-center rounded-xl border bg-white/80 backdrop-blur-sm"
            style={{
              borderColor: `${card.accent}30`,
              boxShadow: `0 4px 12px -4px ${card.accent}20`,
            }}
          >
            <Icon className="size-5" style={{ color: card.accent }} />
          </div>
          <span
            className="rounded-full border px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.14em]"
            style={{
              fontFamily: "var(--font-mono)",
              color: card.accent,
              background: `${card.accent}10`,
              borderColor: `${card.accent}25`,
            }}
          >
            {card.phase}
          </span>
        </div>

        <div className="flex-1">
          <h3
            className="text-[#0A0F1E] leading-tight tracking-tight"
            style={{
              fontFamily: "var(--font-display)",
              fontWeight: 400,
              fontSize: "clamp(22px, 2.4vw, 28px)",
            }}
          >
            {card.title}
          </h3>
          <p className="mt-2 max-w-md text-sm leading-relaxed text-[#5B6478]">
            {card.body}
          </p>
        </div>

        {card.preview && <div className="mt-auto">{card.preview}</div>}
      </div>
    </div>
  )
}
