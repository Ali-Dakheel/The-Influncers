"use client"

import { useRef } from "react"
import { Bot, Send, Sparkles } from "lucide-react"

import { Reveal } from "@/components/reveal"
import { ClientOnly } from "@/components/client-only"
import { BorderBeam } from "@/components/ui/border-beam"
import { Spotlight } from "@/components/ui/spotlight"
import { Meteors } from "@/components/ui/meteors"
import { AnimatedBeam } from "@/components/ui/animated-beam"
import { cn } from "@/lib/utils"

/**
 * The dark Agent slab. The only dark section on the page — used as the visual
 * climax for the moat narrative. Has gradient transitions top + bottom so it
 * feels like a tunnel rather than a hard switch.
 */
export function AgentSlabSection() {
  return (
    <section id="agent" className="relative">
      {/* TOP transition: white → dark, very gentle */}
      <div
        aria-hidden
        className="relative h-40 w-full"
        style={{
          background:
            "linear-gradient(to bottom, #FFFFFF 0%, rgba(220,232,255,0.4) 35%, rgba(26,34,54,0.6) 70%, #0A0F1E 100%)",
        }}
      >
        <div
          className="absolute inset-x-0 bottom-0 h-px"
          style={{
            background:
              "linear-gradient(90deg, transparent, rgba(46,98,217,0.45), transparent)",
          }}
        />
      </div>

      {/* THE SLAB */}
      <div className="dark relative isolate overflow-hidden bg-[#0A0F1E] text-white">
        {/* dot grid */}
        <div
          aria-hidden
          className="pointer-events-none absolute inset-0"
          style={{
            backgroundImage:
              "radial-gradient(circle, rgba(255,255,255,0.045) 1px, transparent 1px)",
            backgroundSize: "44px 44px",
          }}
        />

        {/* Spotlight (top-right) */}
        <Spotlight
          className="-top-40 right-0 md:-top-20 md:right-20"
          fill="rgba(46,98,217,0.55)"
        />

        {/* Meteors — random delays make this client-only to avoid SSR mismatch */}
        <div className="pointer-events-none absolute inset-0 overflow-hidden">
          <ClientOnly>
            <Meteors number={8} />
          </ClientOnly>
        </div>

        {/* Ambient blue glow center-bottom */}
        <div
          aria-hidden
          className="pointer-events-none absolute bottom-0 left-1/2 h-[420px] w-[900px] -translate-x-1/2 rounded-full"
          style={{
            background:
              "radial-gradient(ellipse, rgba(46,98,217,0.18) 0%, transparent 70%)",
            filter: "blur(60px)",
          }}
        />

        <div className="relative mx-auto w-full max-w-7xl px-6 py-32">
          {/* Top header row */}
          <div className="grid gap-16 lg:grid-cols-2 lg:items-center">
            {/* LEFT — copy */}
            <div>
              <Reveal>
                <span
                  className="inline-flex items-center gap-2 rounded-full border border-[#6FA3F5]/25 bg-[#2E62D9]/12 px-3 py-1.5 text-[11px] font-semibold uppercase tracking-[0.18em] text-[#6FA3F5]"
                  style={{ fontFamily: "var(--font-mono)" }}
                >
                  <Sparkles className="size-3" />
                  The moat
                </span>
              </Reveal>

              <Reveal delay={70}>
                <h2
                  className="mt-7 max-w-xl text-balance text-white leading-[1.02] tracking-[-0.025em]"
                  style={{
                    fontFamily: "var(--font-display)",
                    fontWeight: 400,
                    fontSize: "clamp(40px, 5.4vw, 68px)",
                  }}
                >
                  A personal AI agent{" "}
                  <em className="text-[#6FA3F5]">for every creator.</em>
                </h2>
              </Reveal>

              <Reveal delay={140}>
                <p className="mt-6 max-w-md text-base leading-relaxed text-white/55 sm:text-lg">
                  After 10 campaigns, the agent knows a creator better than any
                  platform ever has. Switching means losing it entirely.
                </p>
              </Reveal>

              <Reveal delay={200}>
                <p className="mt-4 max-w-md text-base leading-relaxed text-white/40">
                  It monitors campaigns, predicts fit, writes pitches, submits
                  them, manages every deadline, reviews drafts, and handles
                  communications — autonomously, without a human in the loop.
                </p>
              </Reveal>

              <Reveal delay={260}>
                <div className="mt-10 rounded-2xl border border-white/8 bg-white/[0.04] p-5 backdrop-blur-sm">
                  <div
                    className="text-[10px] font-semibold uppercase tracking-[0.18em] text-white/35"
                    style={{ fontFamily: "var(--font-mono)" }}
                  >
                    The result
                  </div>
                  <p className="mt-2 text-sm leading-relaxed text-white/70">
                    The data moat compounds with every campaign. No competitor
                    can replicate it without years of real outcome data behind
                    them.
                  </p>
                </div>
              </Reveal>
            </div>

            {/* RIGHT — chat with BorderBeam */}
            <Reveal delay={140}>
              <AgentChatCard />
            </Reveal>
          </div>

          {/* MEMORY TIMELINE */}
          <Reveal delay={180} className="mt-24">
            <MemoryTimeline />
          </Reveal>
        </div>
      </div>

      {/* BOTTOM transition: dark → white with sky hint */}
      <div
        aria-hidden
        className="relative h-40 w-full"
        style={{
          background:
            "linear-gradient(to bottom, #0A0F1E 0%, rgba(26,34,54,0.6) 32%, rgba(220,232,255,0.35) 70%, #FFFFFF 100%)",
        }}
      >
        <div
          className="absolute inset-x-0 top-0 h-px"
          style={{
            background:
              "linear-gradient(90deg, transparent, rgba(111,163,245,0.4), transparent)",
          }}
        />
      </div>
    </section>
  )
}

/* ─── Chat card with BorderBeam ─── */
function AgentChatCard() {
  return (
    <div className="relative">
      {/* Soft glow */}
      <div className="pointer-events-none absolute -inset-10 rounded-[40px] bg-[#2E62D9]/15 blur-3xl" />

      <div className="relative overflow-hidden rounded-2xl border border-white/10 bg-[#0E1424]">
        <BorderBeam
          size={120}
          duration={9}
          colorFrom="#2E62D9"
          colorTo="#6FA3F5"
        />
        <BorderBeam
          size={120}
          duration={9}
          delay={4.5}
          colorFrom="#6FA3F5"
          colorTo="#2E62D9"
          reverse
        />

        {/* Header */}
        <div className="flex items-center gap-3 border-b border-white/[0.06] px-5 py-4">
          <div className="flex size-9 items-center justify-center rounded-full bg-gradient-to-br from-[#2E62D9] to-[#1E4FC7]">
            <Bot className="size-4 text-white" />
          </div>
          <div className="flex-1">
            <div className="text-sm font-semibold text-white">
              Agent · @sofia_creates
            </div>
            <div className="text-xs text-white/40">47 campaigns managed</div>
          </div>
          <div className="flex items-center gap-1.5 rounded-full bg-[#10B981]/12 px-2.5 py-1 text-[11px] font-semibold text-[#10B981]">
            <span className="size-1.5 animate-pulse rounded-full bg-[#10B981]" />
            Live
          </div>
        </div>

        {/* Messages */}
        <div className="space-y-3 p-5">
          <div className="text-center text-xs text-white/25">Today · 9:41 AM</div>

          <Bubble side="ai">
            Adidas just dropped a sneaker campaign. Based on your last Nike collab
            at <Token>8.3% engagement</Token> — fit score{" "}
            <Token>94/100</Token>. Want me to write your pitch?
          </Bubble>
          <Bubble side="user">Yes, go for it</Bubble>
          <Bubble side="ai">
            ✓ Pitch written and submitted. Deadline tracked. I'll review the
            brief the moment it arrives.
          </Bubble>
          <Bubble side="ai">
            Estimated payout <Token>$4,200–$5,800</Token>. Based on your last
            three brand deals.
          </Bubble>

          {/* Composer */}
          <div className="mt-2 flex items-center gap-2 rounded-xl border border-white/[0.06] bg-white/[0.03] px-4 py-3">
            <span className="flex-1 select-none text-sm text-white/20">
              Message agent…
            </span>
            <div className="flex size-7 items-center justify-center rounded-lg bg-[#2E62D9]">
              <Send className="size-3 text-white" />
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}

function Bubble({
  side,
  children,
}: {
  side: "ai" | "user"
  children: React.ReactNode
}) {
  return (
    <div
      className={cn(
        "flex",
        side === "ai" ? "justify-start gap-2.5" : "justify-end"
      )}
    >
      {side === "ai" && (
        <div className="mt-0.5 flex size-6 flex-shrink-0 items-center justify-center rounded-full bg-[#2E62D9]">
          <Bot className="size-3 text-white" />
        </div>
      )}
      <div
        className={cn(
          "max-w-[88%] rounded-2xl px-4 py-3 text-sm leading-relaxed",
          side === "ai"
            ? "rounded-tl-sm border border-[#2E62D9]/20 bg-[#2E62D9]/12 text-white/75"
            : "rounded-tr-sm border border-white/[0.06] bg-white/[0.05] text-white/55"
        )}
      >
        {children}
      </div>
    </div>
  )
}

function Token({ children }: { children: React.ReactNode }) {
  return <span className="font-semibold text-[#6FA3F5]">{children}</span>
}

/* ─── Memory timeline (10 nodes connected by an animated beam) ─── */

const MEMORY_NODES = [
  { n: "01", label: "Posting cadence" },
  { n: "02", label: "Voice patterns" },
  { n: "03", label: "Brand affinity" },
  { n: "04", label: "Pricing model" },
  { n: "05", label: "Audience overlap" },
  { n: "06", label: "Best-time data" },
  { n: "07", label: "Pitch templates" },
  { n: "08", label: "Brief preferences" },
  { n: "09", label: "Negotiation logic" },
  { n: "10", label: "Writes in your voice" },
]

function MemoryTimeline() {
  const containerRef = useRef<HTMLDivElement>(null)
  const fromRef = useRef<HTMLDivElement>(null)
  const toRef = useRef<HTMLDivElement>(null)

  return (
    <div
      ref={containerRef}
      className="relative rounded-3xl border border-white/[0.06] bg-white/[0.02] p-8 backdrop-blur-sm"
    >
      <div className="mb-8 flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-end">
        <div>
          <div
            className="text-[10px] font-semibold uppercase tracking-[0.18em] text-white/35"
            style={{ fontFamily: "var(--font-mono)" }}
          >
            Memory timeline
          </div>
          <h3
            className="mt-1.5 text-white"
            style={{
              fontFamily: "var(--font-display)",
              fontWeight: 400,
              fontSize: "clamp(20px, 2.4vw, 28px)",
            }}
          >
            What ten campaigns teach the agent.
          </h3>
        </div>
        <p className="max-w-md text-sm text-white/45">
          Each campaign deepens what the agent knows. By the tenth, leaving the
          platform means losing a workflow that runs itself.
        </p>
      </div>

      <div className="relative">
        {/* The 10 nodes */}
        <div className="relative z-10 grid grid-cols-5 gap-4 sm:grid-cols-10">
          {MEMORY_NODES.map((node, i) => {
            const isFirst = i === 0
            const isLast = i === MEMORY_NODES.length - 1
            return (
              <div
                key={node.n}
                ref={isFirst ? fromRef : isLast ? toRef : undefined}
                className="relative flex flex-col items-center gap-2"
              >
                <div
                  className={cn(
                    "relative flex size-10 items-center justify-center rounded-full border text-[11px] font-bold transition-colors",
                    isLast
                      ? "border-[#6FA3F5] bg-[#2E62D9] text-white shadow-[0_0_24px_rgba(46,98,217,0.6)]"
                      : "border-white/15 bg-[#0E1424] text-white/55"
                  )}
                  style={{ fontFamily: "var(--font-mono)" }}
                >
                  {node.n}
                  {isLast && (
                    <span className="absolute -inset-1 -z-10 rounded-full bg-[#2E62D9]/30 blur-md" />
                  )}
                </div>
                <span
                  className={cn(
                    "text-center text-[10px] leading-tight",
                    isLast ? "text-[#6FA3F5] font-semibold" : "text-white/40"
                  )}
                >
                  {node.label}
                </span>
              </div>
            )
          })}
        </div>

        {/* Animated beam from node 1 → node 10 */}
        <AnimatedBeam
          containerRef={containerRef}
          fromRef={fromRef}
          toRef={toRef}
          curvature={0}
          duration={5}
          pathColor="#1A2236"
          pathWidth={2}
          pathOpacity={1}
          gradientStartColor="#2E62D9"
          gradientStopColor="#6FA3F5"
          startYOffset={-32}
          endYOffset={-32}
        />
      </div>
    </div>
  )
}
