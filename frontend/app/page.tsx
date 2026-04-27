import { Nav } from "@/components/nav"
import { Footer } from "@/components/footer"

import { HeroSection } from "@/components/sections/hero"
import { LogoCloudSection } from "@/components/sections/logo-cloud"
import { OldVsNewSection } from "@/components/sections/old-vs-new"
import { BentoSection } from "@/components/sections/bento"
import { MatchingDeepDiveSection } from "@/components/sections/matching-deep-dive"
import { ReviewerDeepDiveSection } from "@/components/sections/reviewer-deep-dive"
import { AgentSlabSection } from "@/components/sections/agent-slab"
import { WorkflowSection } from "@/components/sections/workflow"
import { FlywheelSection } from "@/components/sections/flywheel"
import { StatsSection } from "@/components/sections/stats"
import { WaitlistSection } from "@/components/sections/waitlist"
import { ClosingCtaSection } from "@/components/sections/closing-cta"

/**
 * The Influncers — Stripe-aesthetic landing page.
 * Twelve sections, light theme with one dark "moat" climax slab.
 */
export default function Page() {
  return (
    <>
      <Nav />

      <main>
        <HeroSection />
        <LogoCloudSection />
        <OldVsNewSection />
        <BentoSection />
        <MatchingDeepDiveSection />
        <ReviewerDeepDiveSection />
        <AgentSlabSection />
        <WorkflowSection />
        <FlywheelSection />
        <StatsSection />
        <WaitlistSection />
        <ClosingCtaSection />
      </main>

      <Footer />
    </>
  )
}
