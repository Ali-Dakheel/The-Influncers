"use client"

import { ReactNode } from "react"
import {
  TrendingUp,
  BarChart2,
  Cpu,
  Store,
  Bot,
  Target,
} from "lucide-react"

/**
 * A single icon orbiting at a fixed radius. CSS-only — uses the `orbit-rotate`
 * keyframe and a counter-rotation on the inner element so the icon stays upright.
 */
function OrbitItem({
  radius,
  duration,
  startFraction = 0,
  reverse = false,
  children,
}: {
  radius: number
  duration: number
  startFraction?: number
  reverse?: boolean
  children: ReactNode
}) {
  const delay = -startFraction * duration
  const dir = reverse ? "reverse" : "normal"
  const counterDir = reverse ? "normal" : "reverse"
  return (
    <div
      className="absolute inset-0 flex items-center justify-center"
      style={{ animation: `orbit-rotate ${duration}s linear ${delay}s infinite ${dir}` }}
    >
      <div style={{ transform: `translateX(${radius}px)` }}>
        <div style={{ animation: `orbit-rotate ${duration}s linear ${delay}s infinite ${counterDir}` }}>
          {children}
        </div>
      </div>
    </div>
  )
}

/**
 * Two concentric orbits of icons, contra-rotating, with a glowing brand core.
 * Recolored from the original dark variant to a Stripe-style light theme:
 *  - tile bg #FFFFFF, hairline #E8EAF0
 *  - inner ring icons in solid brand
 *  - outer ring icons in muted ink
 *  - dashed orbit guides in primary-soft
 */
export function OrbitFlywheelDiagram() {
  const inner = [
    { Icon: TrendingUp, sf: 0 },
    { Icon: BarChart2, sf: 1 / 3 },
    { Icon: Cpu, sf: 2 / 3 },
  ]
  const outer = [
    { Icon: Store, sf: 1 / 6 },
    { Icon: Bot, sf: 1 / 6 + 1 / 3 },
    { Icon: Target, sf: 1 / 6 + 2 / 3 },
  ]

  return (
    <div className="relative mx-auto aspect-square w-full max-w-[400px]">
      <svg className="absolute inset-0 h-full w-full" viewBox="0 0 360 360">
        {/* dashed orbits */}
        <circle
          cx={180}
          cy={180}
          r={86}
          fill="none"
          stroke="rgba(46,98,217,0.14)"
          strokeWidth="1"
          strokeDasharray="3 9"
        />
        <circle
          cx={180}
          cy={180}
          r={148}
          fill="none"
          stroke="rgba(46,98,217,0.08)"
          strokeWidth="1"
          strokeDasharray="2 14"
        />
        {/* core glow */}
        <circle cx={180} cy={180} r={42} fill="rgba(46,98,217,0.06)" />
        <circle
          cx={180}
          cy={180}
          r={28}
          fill="rgba(46,98,217,0.10)"
          stroke="rgba(46,98,217,0.32)"
          strokeWidth="1.5"
        />
      </svg>

      {/* core label */}
      <div className="pointer-events-none absolute inset-0 flex flex-col items-center justify-center gap-0.5">
        <div
          className="leading-none font-bold text-[#2E62D9]"
          style={{ fontSize: "8px", letterSpacing: "0.18em" }}
        >
          THE
        </div>
        <div
          className="leading-none font-bold text-[#2E62D9]"
          style={{ fontSize: "8px", letterSpacing: "0.18em" }}
        >
          LOOP
        </div>
      </div>

      {/* inner ring */}
      {inner.map((item, i) => (
        <OrbitItem key={`in-${i}`} radius={86} duration={22} startFraction={item.sf}>
          <div
            className="flex h-10 w-10 items-center justify-center rounded-xl bg-white"
            style={{
              border: "1px solid rgba(46,98,217,0.30)",
              boxShadow: "0 4px 12px rgba(46,98,217,0.08), 0 0 0 1px rgba(46,98,217,0.04)",
            }}
          >
            <item.Icon size={17} style={{ color: "#2E62D9" }} />
          </div>
        </OrbitItem>
      ))}

      {/* outer ring */}
      {outer.map((item, i) => (
        <OrbitItem
          key={`out-${i}`}
          radius={148}
          duration={38}
          startFraction={item.sf}
          reverse
        >
          <div
            className="flex h-10 w-10 items-center justify-center rounded-xl bg-white"
            style={{
              border: "1px solid #E8EAF0",
              boxShadow: "0 2px 8px rgba(10,15,30,0.04)",
            }}
          >
            <item.Icon size={17} style={{ color: "#5B6478" }} />
          </div>
        </OrbitItem>
      ))}
    </div>
  )
}
