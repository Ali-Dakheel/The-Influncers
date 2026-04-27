"use client"

import { useEffect, useState } from "react"
import Link from "next/link"
import { ArrowRight, Menu } from "lucide-react"

import { Button } from "@/components/ui/button"
import { LogoMark } from "@/components/logo-mark"
import { cn } from "@/lib/utils"

const NAV_LINKS = [
  { label: "Platform", href: "#platform" },
  { label: "How it works", href: "#workflow" },
  { label: "Pricing", href: "#pricing" },
  { label: "Company", href: "#company" },
]

export function Nav() {
  const [scrolled, setScrolled] = useState(false)

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 28)
    onScroll()
    window.addEventListener("scroll", onScroll, { passive: true })
    return () => window.removeEventListener("scroll", onScroll)
  }, [])

  return (
    <header
      className={cn(
        "fixed top-0 left-0 right-0 z-50 transition-all duration-300",
        scrolled
          ? "bg-white/72 backdrop-blur-xl border-b border-[#E8EAF0]/80"
          : "bg-transparent"
      )}
    >
      <div className="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-6">
        {/* Brand */}
        <Link href="/" className="group flex items-center gap-2.5">
          <LogoMark size={28} tone="brand" />
          <span
            className="text-[#0A0F1E] text-lg leading-none tracking-tight"
            style={{ fontFamily: "var(--font-display)" }}
          >
            The Influncers
          </span>
        </Link>

        {/* Links */}
        <nav className="hidden md:flex items-center gap-1">
          {NAV_LINKS.map((link) => (
            <Link
              key={link.href}
              href={link.href}
              className="relative rounded-md px-3.5 py-2 text-sm text-[#5B6478] transition-colors hover:text-[#0A0F1E]"
            >
              {link.label}
            </Link>
          ))}
        </nav>

        {/* CTAs */}
        <div className="flex items-center gap-2">
          <Button
            variant="ghost"
            size="sm"
            className="hidden sm:inline-flex text-[#5B6478] hover:text-[#0A0F1E] hover:bg-transparent"
          >
            Sign in
          </Button>
          <Button
            size="sm"
            className="bg-[#0A0F1E] text-white hover:bg-[#1A2236] gap-1.5 px-4"
          >
            Request access
            <ArrowRight className="size-3.5" />
          </Button>
          <Button
            variant="ghost"
            size="icon-sm"
            className="md:hidden text-[#0A0F1E]"
            aria-label="Open menu"
          >
            <Menu className="size-4" />
          </Button>
        </div>
      </div>
    </header>
  )
}
