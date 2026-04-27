import { ReactNode } from "react"
import { cn } from "@/lib/utils"

/**
 * Section primitive — controls the surface tone, vertical rhythm, and full-bleed
 * behavior consistently across the page.
 *
 * `tone` maps to one of the design-token surfaces:
 *  - light       → white #FFFFFF
 *  - subtle      → off-white #FBFBFC (logo strip, stats)
 *  - tinted      → cool tinted #F6F7FB (bento canvas)
 *  - warm        → cream #FBF7F2 (workflow, soft sections)
 *  - dark        → ink #0A0F1E (the Agent slab — only one)
 */
export function Section({
  children,
  tone = "light",
  className,
  containerClassName,
  fullBleed = false,
  id,
  as: Tag = "section",
}: {
  children: ReactNode
  tone?: "light" | "subtle" | "tinted" | "warm" | "dark"
  className?: string
  containerClassName?: string
  fullBleed?: boolean
  id?: string
  as?: "section" | "footer" | "div"
}) {
  const surfaces: Record<string, string> = {
    light: "bg-[#FFFFFF] text-[#0A0F1E]",
    subtle: "bg-[#FBFBFC] text-[#0A0F1E]",
    tinted: "bg-[#F6F7FB] text-[#0A0F1E]",
    warm: "bg-[#FBF7F2] text-[#0A0F1E]",
    dark: "bg-[#0A0F1E] text-white dark",
  }

  return (
    <Tag
      id={id}
      className={cn(
        "relative isolate overflow-hidden",
        surfaces[tone],
        className
      )}
    >
      {fullBleed ? (
        children
      ) : (
        <div
          className={cn(
            "relative mx-auto w-full max-w-7xl px-6 py-24 sm:py-28 lg:py-32",
            containerClassName
          )}
        >
          {children}
        </div>
      )}
    </Tag>
  )
}

/**
 * Eyebrow — small uppercase tag used above section headlines.
 * Pairs with `<Section>` and the editorial display headlines.
 */
export function Eyebrow({
  children,
  tone = "brand",
  className,
}: {
  children: ReactNode
  tone?: "brand" | "ink" | "muted"
  className?: string
}) {
  const tones: Record<string, string> = {
    brand: "text-[#2E62D9] bg-[#2E62D9]/[0.06] border-[#2E62D9]/15",
    ink: "text-white/70 bg-white/[0.06] border-white/[0.10]",
    muted: "text-[#5B6478] bg-[#0A0F1E]/[0.04] border-[#0A0F1E]/[0.08]",
  }
  return (
    <span
      className={cn(
        "inline-flex items-center gap-2 rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.18em]",
        tones[tone],
        className
      )}
      style={{ fontFamily: "var(--font-mono)" }}
    >
      {children}
    </span>
  )
}
