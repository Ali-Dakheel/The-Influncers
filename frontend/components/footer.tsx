import Link from "next/link"
import { LogoMark } from "@/components/logo-mark"

const FOOTER_COLUMNS = [
  {
    heading: "Product",
    links: [
      { label: "Platform overview", href: "#platform" },
      { label: "Campaign Marketplace", href: "#" },
      { label: "Creator OS", href: "#" },
      { label: "AI Agent", href: "#agent" },
      { label: "Matching Engine", href: "#" },
      { label: "Pricing", href: "#pricing" },
    ],
  },
  {
    heading: "Resources",
    links: [
      { label: "Documentation", href: "#" },
      { label: "API reference", href: "#" },
      { label: "Changelog", href: "#" },
      { label: "Status", href: "#" },
      { label: "Brand assets", href: "#" },
    ],
  },
  {
    heading: "Company",
    links: [
      { label: "About", href: "#" },
      { label: "Customers", href: "#" },
      { label: "Careers", href: "#" },
      { label: "Contact", href: "#" },
    ],
  },
  {
    heading: "Legal",
    links: [
      { label: "Privacy policy", href: "#" },
      { label: "Terms of service", href: "#" },
      { label: "Security", href: "#" },
      { label: "Acceptable use", href: "#" },
    ],
  },
]

export function Footer() {
  return (
    <footer className="border-t border-[#E8EAF0] bg-white">
      <div className="mx-auto w-full max-w-7xl px-6 py-16">
        <div className="grid grid-cols-2 gap-10 sm:grid-cols-4 lg:grid-cols-5 lg:gap-8">
          {/* Brand block */}
          <div className="col-span-2 sm:col-span-4 lg:col-span-1">
            <Link href="/" className="inline-flex items-center gap-2.5">
              <LogoMark size={28} tone="brand" />
              <span
                className="text-[#0A0F1E] text-lg leading-none tracking-tight"
                style={{ fontFamily: "var(--font-display)" }}
              >
                The Influncers
              </span>
            </Link>
            <p className="mt-4 max-w-[260px] text-sm leading-relaxed text-[#5B6478]">
              The first AI-native influencer marketing platform. Built so the data moat
              compounds with every campaign.
            </p>
          </div>

          {/* Columns */}
          {FOOTER_COLUMNS.map((col) => (
            <div key={col.heading}>
              <div
                className="text-[11px] font-semibold uppercase tracking-[0.18em] text-[#94A0B8]"
                style={{ fontFamily: "var(--font-mono)" }}
              >
                {col.heading}
              </div>
              <ul className="mt-4 space-y-2.5">
                {col.links.map((link) => (
                  <li key={link.label}>
                    <Link
                      href={link.href}
                      className="text-sm text-[#5B6478] transition-colors hover:text-[#0A0F1E]"
                    >
                      {link.label}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        {/* Sub-footer */}
        <div className="mt-14 flex flex-col items-start justify-between gap-4 border-t border-[#E8EAF0] pt-8 sm:flex-row sm:items-center">
          <p className="text-xs text-[#94A0B8]">
            © {new Date().getFullYear()} The Influncers. AI-native influencer
            marketing infrastructure.
          </p>
          <div className="flex items-center gap-6">
            <span
              className="text-[10px] font-semibold uppercase tracking-[0.18em] text-[#94A0B8]"
              style={{ fontFamily: "var(--font-mono)" }}
            >
              Stockholm · Bahrain
            </span>
            <Link
              href="#"
              className="text-xs text-[#5B6478] hover:text-[#0A0F1E] transition-colors"
            >
              Status
            </Link>
            <Link
              href="#"
              className="text-xs text-[#5B6478] hover:text-[#0A0F1E] transition-colors"
            >
              Twitter
            </Link>
            <Link
              href="#"
              className="text-xs text-[#5B6478] hover:text-[#0A0F1E] transition-colors"
            >
              LinkedIn
            </Link>
          </div>
        </div>
      </div>
    </footer>
  )
}
