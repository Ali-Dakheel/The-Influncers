"use client"

import { useRef } from "react"
import {
  Bot,
  Camera,
  CheckCircle2,
  FileText,
  Music,
  Upload,
} from "lucide-react"

import { Eyebrow } from "@/components/section"
import { Reveal } from "@/components/reveal"
import { MeshBackdrop } from "@/components/mesh-backdrop"
import { AnimatedBeam } from "@/components/ui/animated-beam"
import { cn } from "@/lib/utils"

export function ReviewerDeepDiveSection() {
  return (
    <section className="relative overflow-hidden bg-white">
      <MeshBackdrop palette="lavender" />

      <div className="relative mx-auto w-full max-w-7xl px-6 py-24 lg:py-32">
        <div className="grid gap-16 lg:grid-cols-2 lg:items-center">
          {/* LEFT — vertical pipeline */}
          <Reveal>
            <ReviewPipeline />
          </Reveal>

          {/* RIGHT — copy */}
          <div>
            <Reveal>
              <Eyebrow>
                <Bot className="size-3" /> AI Draft Reviewer
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
                Every draft, reviewed.{" "}
                <em className="text-[#2E62D9]">In sixty seconds.</em>
              </h2>
            </Reveal>
            <Reveal delay={140}>
              <p className="mt-6 max-w-md text-base leading-relaxed text-[#5B6478]">
                Wrong environment, audio glitches, missing brand mentions,
                off-brief framing — caught automatically before any human looks.
                The reviewer runs five checks in parallel, every time, on every
                draft.
              </p>
            </Reveal>

            <div className="mt-10 grid grid-cols-2 gap-4 max-w-md">
              <Reveal delay={180}>
                <Stat label="Avg review time" value="42s" tone="success" />
              </Reveal>
              <Reveal delay={240}>
                <Stat label="Drafts auto-approved" value="71%" tone="brand" />
              </Reveal>
              <Reveal delay={300}>
                <Stat label="Issues caught pre-human" value="100%" tone="success" />
              </Reveal>
              <Reveal delay={360}>
                <Stat label="Human hours saved / mo" value="380" tone="brand" />
              </Reveal>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}

/* ─── Vertical pipeline visualization ─── */

const STEPS = [
  { Icon: Upload, label: "Upload", meta: "creator submits", color: "#5B6478" },
  { Icon: Camera, label: "Frame check", meta: "environment, lighting", color: "#2E62D9" },
  { Icon: Music, label: "Audio check", meta: "clipping, levels, music", color: "#7C3AED" },
  { Icon: FileText, label: "Brief match", meta: "talking points, hooks", color: "#047857" },
  { Icon: CheckCircle2, label: "Verdict", meta: "approved · 42s", color: "#10B981" },
]

function ReviewPipeline() {
  const containerRef = useRef<HTMLDivElement>(null)
  // STEPS has fixed length 5; explicit refs keep rules-of-hooks happy.
  const r0 = useRef<HTMLDivElement>(null)
  const r1 = useRef<HTMLDivElement>(null)
  const r2 = useRef<HTMLDivElement>(null)
  const r3 = useRef<HTMLDivElement>(null)
  const r4 = useRef<HTMLDivElement>(null)
  const refs = [r0, r1, r2, r3, r4]

  return (
    <div
      ref={containerRef}
      className="relative mx-auto w-full max-w-md rounded-3xl border border-[#0A0F1E]/8 bg-white p-6"
      style={{
        boxShadow:
          "0 32px 64px -28px rgba(10,15,30,0.14), 0 0 0 1px rgba(10,15,30,0.04), 0 4px 12px rgba(10,15,30,0.04)",
      }}
    >
      <div className="flex items-center justify-between border-b border-[#E8EAF0] pb-4">
        <div
          className="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#94A0B8]"
          style={{ fontFamily: "var(--font-mono)" }}
        >
          Pipeline · Run #2,847
        </div>
        <span className="rounded-full bg-[#10B981]/12 px-2.5 py-1 text-[10px] font-semibold text-[#10B981]">
          Approved
        </span>
      </div>

      <div className="relative flex flex-col gap-3 pt-5">
        {STEPS.map((step, i) => {
          const isLast = i === STEPS.length - 1
          return (
            <div
              key={step.label}
              ref={refs[i]}
              className="relative z-10 flex items-center gap-3 rounded-xl border border-[#E8EAF0] bg-white px-4 py-3"
            >
              <div
                className={cn(
                  "flex size-9 items-center justify-center rounded-lg",
                  isLast && "ring-2 ring-[#10B981]/20"
                )}
                style={{
                  background: `${step.color}10`,
                }}
              >
                <step.Icon className="size-4" style={{ color: step.color }} />
              </div>
              <div className="flex-1">
                <div className="text-[13px] font-semibold text-[#0A0F1E]">
                  {step.label}
                </div>
                <div className="text-xs text-[#94A0B8]">{step.meta}</div>
              </div>
              {!isLast ? (
                <CheckCircle2 className="size-4 text-[#10B981]" />
              ) : (
                <span className="text-[10px] font-bold uppercase tracking-[0.14em] text-[#10B981]">
                  Done
                </span>
              )}
            </div>
          )
        })}
      </div>

      {/* Animated beams between consecutive steps */}
      {refs.slice(0, -1).map((fromRef, i) => (
        <AnimatedBeam
          key={i}
          containerRef={containerRef}
          fromRef={fromRef}
          toRef={refs[i + 1]}
          curvature={-40}
          duration={2.4}
          delay={i * 0.4}
          pathColor="#E8EAF0"
          pathOpacity={1}
          pathWidth={2}
          gradientStartColor="#2E62D9"
          gradientStopColor="#6FA3F5"
          startYOffset={0}
          endYOffset={0}
        />
      ))}
    </div>
  )
}

function Stat({
  label,
  value,
  tone,
}: {
  label: string
  value: string
  tone: "brand" | "success"
}) {
  const color = tone === "brand" ? "#2E62D9" : "#10B981"
  return (
    <div className="rounded-2xl border border-[#E8EAF0] bg-white px-4 py-3">
      <div
        className="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#94A0B8]"
        style={{ fontFamily: "var(--font-mono)" }}
      >
        {label}
      </div>
      <div
        className="mt-1.5 text-2xl leading-none"
        style={{
          fontFamily: "var(--font-display)",
          fontWeight: 400,
          color,
        }}
      >
        {value}
      </div>
    </div>
  )
}
