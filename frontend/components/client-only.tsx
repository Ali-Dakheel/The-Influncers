"use client"

import { useEffect, useState, type ReactNode } from "react"

/**
 * Renders children only after the component has mounted on the client.
 * Used to avoid hydration mismatches for components that produce
 * non-deterministic output during render (random delays, time-based values,
 * etc). The server-rendered output is `null` so the markup matches; after
 * hydration the children are mounted client-side.
 */
export function ClientOnly({ children }: { children: ReactNode }) {
  const [mounted, setMounted] = useState(false)
  useEffect(() => setMounted(true), [])
  if (!mounted) return null
  return <>{children}</>
}
