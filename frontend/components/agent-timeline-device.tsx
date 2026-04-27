"use client"

import { Bot, Check, MessagesSquare, Send } from "lucide-react"

/**
 * Agent timeline "device" — the floating Stripe-style mock that lives on the
 * right side of the hero. Shows a few timeline entries from a creator's
 * personal agent: pitches, drafts, reviews, payouts.
 */
export function AgentTimelineDevice() {
  return (
    <div className="relative">
      {/* Soft mesh glow behind */}
      <div className="pointer-events-none absolute -inset-12 rounded-[40px] bg-gradient-to-br from-[#FFE4D6]/55 via-[#DCE8FF]/45 to-[#D9F2E8]/55 blur-3xl" />

      <div
        className="relative animate-float-slow overflow-hidden rounded-2xl border border-[#0A0F1E]/8 bg-white"
        style={{
          boxShadow:
            "0 32px 64px -28px rgba(10,15,30,0.18), 0 0 0 1px rgba(10,15,30,0.04), 0 4px 12px rgba(10,15,30,0.04)",
        }}
      >
        {/* Window chrome */}
        <div className="flex items-center gap-2 border-b border-[#0A0F1E]/6 px-4 py-3">
          <div className="flex gap-1.5">
            <div className="size-2.5 rounded-full bg-[#0A0F1E]/8" />
            <div className="size-2.5 rounded-full bg-[#0A0F1E]/8" />
            <div className="size-2.5 rounded-full bg-[#0A0F1E]/8" />
          </div>
          <div
            className="flex-1 text-center text-[10px] font-semibold uppercase tracking-[0.16em] text-[#94A0B8]"
            style={{ fontFamily: "var(--font-mono)" }}
          >
            agent · @sofia_creates
          </div>
          <div className="size-3" />
        </div>

        {/* Header */}
        <div className="flex items-center gap-3 border-b border-[#0A0F1E]/6 px-5 py-4">
          <div className="flex size-10 items-center justify-center rounded-full bg-gradient-to-br from-[#2E62D9] to-[#1E4FC7] text-sm font-semibold text-white">
            SC
          </div>
          <div className="min-w-0 flex-1">
            <div className="text-sm font-semibold text-[#0A0F1E]">Sofia Creates</div>
            <div className="text-xs text-[#94A0B8]">47 campaigns managed</div>
          </div>
          <div className="flex items-center gap-1.5 rounded-full bg-[#10B981]/8 px-2.5 py-1">
            <span className="size-1.5 animate-pulse rounded-full bg-[#10B981]" />
            <span className="text-[11px] font-semibold text-[#10B981]">Live</span>
          </div>
        </div>

        {/* Timeline entries */}
        <div className="divide-y divide-[#0A0F1E]/5">
          <TimelineEntry
            icon={<Bot className="size-3.5" />}
            iconClass="bg-[#2E62D9]/10 text-[#2E62D9]"
            title="Adidas — Sneaker Drop"
            meta="Pitch submitted · fit 94/100"
            time="9:41 AM"
            status="live"
          />
          <TimelineEntry
            icon={<MessagesSquare className="size-3.5" />}
            iconClass="bg-[#7C3AED]/10 text-[#7C3AED]"
            title="Nike × Sofia"
            meta="Brief approved · drafting now"
            time="9:08 AM"
            status="active"
          />
          <TimelineEntry
            icon={<Check className="size-3.5" />}
            iconClass="bg-[#10B981]/10 text-[#10B981]"
            title="Lululemon — Studio"
            meta="Draft reviewed in 42s · approved"
            time="Yesterday"
            status="done"
          />
        </div>

        {/* Composer */}
        <div className="flex items-center gap-2 border-t border-[#0A0F1E]/6 bg-[#FBFBFC] px-4 py-3">
          <div className="flex-1 text-sm text-[#94A0B8]">Ask the agent…</div>
          <div className="flex size-7 items-center justify-center rounded-md bg-[#0A0F1E] text-white">
            <Send className="size-3" />
          </div>
        </div>
      </div>

      {/* Floating chip */}
      <div
        className="animate-float absolute -right-6 -top-5 hidden rounded-2xl border border-[#0A0F1E]/8 bg-white px-4 py-3 sm:block"
        style={{
          boxShadow: "0 20px 40px -20px rgba(10,15,30,0.12), 0 0 0 1px rgba(10,15,30,0.03)",
        }}
      >
        <div className="text-[10px] uppercase tracking-[0.16em] text-[#94A0B8]" style={{ fontFamily: "var(--font-mono)" }}>
          predicted engagement
        </div>
        <div className="mt-1 text-sm font-semibold text-[#2E62D9]">7.8 – 9.1%</div>
      </div>

      {/* Floating chip 2 */}
      <div
        className="animate-float absolute -bottom-5 -left-6 hidden rounded-2xl border border-[#0A0F1E]/8 bg-white px-4 py-3 sm:block"
        style={{
          animationDelay: "2.5s",
          boxShadow: "0 20px 40px -20px rgba(10,15,30,0.12), 0 0 0 1px rgba(10,15,30,0.03)",
        }}
      >
        <div className="text-[10px] uppercase tracking-[0.16em] text-[#94A0B8]" style={{ fontFamily: "var(--font-mono)" }}>
          draft reviewed in
        </div>
        <div className="mt-1 text-sm font-semibold text-[#10B981]">42 seconds</div>
      </div>
    </div>
  )
}

function TimelineEntry({
  icon,
  iconClass,
  title,
  meta,
  time,
  status,
}: {
  icon: React.ReactNode
  iconClass: string
  title: string
  meta: string
  time: string
  status: "live" | "active" | "done"
}) {
  const statusBadge = {
    live: "bg-[#10B981]/10 text-[#10B981]",
    active: "bg-[#2E62D9]/10 text-[#2E62D9]",
    done: "bg-[#0A0F1E]/6 text-[#5B6478]",
  }[status]
  const statusLabel = {
    live: "Live",
    active: "Active",
    done: "Done",
  }[status]
  return (
    <div className="flex items-center gap-3 px-5 py-3.5">
      <div className={`flex size-7 items-center justify-center rounded-lg ${iconClass}`}>
        {icon}
      </div>
      <div className="min-w-0 flex-1">
        <div className="truncate text-[13px] font-semibold text-[#0A0F1E]">{title}</div>
        <div className="truncate text-xs text-[#94A0B8]">{meta}</div>
      </div>
      <div className="flex flex-col items-end gap-1">
        <span className={`rounded-full px-2 py-0.5 text-[10px] font-semibold ${statusBadge}`}>
          {statusLabel}
        </span>
        <span className="text-[10px] text-[#94A0B8]">{time}</span>
      </div>
    </div>
  )
}
