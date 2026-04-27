import { cn } from "@/lib/utils"

/**
 * MeshBackdrop — three absolutely-positioned blurred radial blobs from the mesh
 * palette. Use as the first child of a relative section to provide that signature
 * Stripe pastel-mesh atmosphere.
 *
 * Pick a `palette` preset or supply a custom array of three colors + positions.
 */

type MeshBlob = {
  color: string
  /** percent values, e.g. "30%" "70%" */
  x: string
  y: string
  /** size in CSS units, e.g. "640px" */
  size: string
  /** 0..1 — how saturated the blob is */
  opacity?: number
}

const PALETTES: Record<string, MeshBlob[]> = {
  // Hero — barely-there sky + peach hint anchored top
  hero: [
    { color: "var(--mesh-sky)", x: "20%", y: "30%", size: "720px", opacity: 0.45 },
    { color: "var(--mesh-peach)", x: "80%", y: "20%", size: "560px", opacity: 0.32 },
    { color: "var(--mesh-lavender)", x: "60%", y: "100%", size: "640px", opacity: 0.28 },
  ],
  // Closing CTA — mirrors hero, even softer
  cta: [
    { color: "var(--mesh-sky)", x: "30%", y: "40%", size: "560px", opacity: 0.35 },
    { color: "var(--mesh-blush)", x: "70%", y: "60%", size: "560px", opacity: 0.22 },
  ],
  // Single soft mint blob, top-left anchor (used behind data viz)
  mint: [
    { color: "var(--mesh-mint)", x: "-10%", y: "30%", size: "520px", opacity: 0.32 },
  ],
  // Single soft lavender blob, top-right anchor
  lavender: [
    { color: "var(--mesh-lavender)", x: "110%", y: "30%", size: "520px", opacity: 0.28 },
  ],
  // Sky blob bottom-right (flywheel)
  sky: [
    { color: "var(--mesh-sky)", x: "90%", y: "100%", size: "560px", opacity: 0.30 },
  ],
}

export function MeshBackdrop({
  palette = "hero",
  blobs,
  className,
  drift = true,
}: {
  palette?: keyof typeof PALETTES
  blobs?: MeshBlob[]
  className?: string
  drift?: boolean
}) {
  const list = blobs ?? PALETTES[palette]
  return (
    <div
      aria-hidden
      className={cn("pointer-events-none absolute inset-0 overflow-hidden", className)}
    >
      {list.map((b, i) => (
        <div
          key={i}
          className={cn(drift && "animate-mesh-drift")}
          style={{
            position: "absolute",
            left: b.x,
            top: b.y,
            width: b.size,
            height: b.size,
            transform: "translate(-50%, -50%)",
            background: `radial-gradient(closest-side, ${b.color} 0%, transparent 72%)`,
            opacity: b.opacity ?? 0.6,
            filter: "blur(80px)",
            animationDelay: `${i * 4}s`,
            mixBlendMode: "multiply",
          }}
        />
      ))}
    </div>
  )
}
