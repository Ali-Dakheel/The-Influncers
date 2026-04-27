/**
 * Brand mark — three connected nodes forming an upward triangle.
 * The triangle reads as: brand (top) × creator (bottom-left) × audience (bottom-right).
 *
 * Two visual variants:
 *  - tone="brand"  → blue tile, white nodes (use on light backgrounds)
 *  - tone="ink"    → ink tile, sky nodes (use on the rare warm/muted card)
 *  - tone="ghost"  → no tile, ink nodes (use inside dense headers)
 */
export function LogoMark({
  size = 30,
  tone = "brand",
}: {
  size?: number
  tone?: "brand" | "ink" | "ghost"
}) {
  const palette = {
    brand: { tile: "#2E62D9", node: "#FFFFFF", nodeAlpha: 0.7, line: "rgba(255,255,255,0.32)" },
    ink: { tile: "#0A0F1E", node: "#6FA3F5", nodeAlpha: 0.75, line: "rgba(111,163,245,0.32)" },
    ghost: { tile: "transparent", node: "#0A0F1E", nodeAlpha: 0.65, line: "rgba(10,15,30,0.28)" },
  }[tone]

  return (
    <svg
      width={size}
      height={size}
      viewBox="0 0 32 32"
      fill="none"
      aria-label="The Influncers logo"
      role="img"
    >
      {tone !== "ghost" && <rect width="32" height="32" rx="8" fill={palette.tile} />}
      <line
        x1="16"
        y1="9.5"
        x2="8.5"
        y2="23"
        stroke={palette.line}
        strokeWidth="1.3"
        strokeLinecap="round"
      />
      <line
        x1="16"
        y1="9.5"
        x2="23.5"
        y2="23"
        stroke={palette.line}
        strokeWidth="1.3"
        strokeLinecap="round"
      />
      <line
        x1="8.5"
        y1="23"
        x2="23.5"
        y2="23"
        stroke={palette.line}
        strokeWidth="1.3"
        strokeLinecap="round"
      />
      <circle cx="16" cy="9.5" r="2.8" fill={palette.node} />
      <circle cx="8.5" cy="23" r="2.2" fill={palette.node} fillOpacity={palette.nodeAlpha} />
      <circle cx="23.5" cy="23" r="2.2" fill={palette.node} fillOpacity={palette.nodeAlpha} />
    </svg>
  )
}
