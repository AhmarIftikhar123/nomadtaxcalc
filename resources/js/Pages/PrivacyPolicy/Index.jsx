import React from "react";
import { usePage } from "@inertiajs/react";
import { Head } from "@inertiajs/react";
import TaxCalculatorLayout from "@/Layouts/TaxCalculatorLayout";
import Breadcrumbs from "@/Components/Ui/Breadcrumbs";
import TableOfContents from "@/Components/Ui/TableOfContents";
import DisclaimerBanner from "@/Components/Ui/DisclaimerBanner";
import {
    PolicySection,
    Lead,
    PolicyList,
    ContactCard,
    PolicyDivider,
} from "./components";

// ---------------------------------------------------------------------------
// Page-level constants
// ---------------------------------------------------------------------------

const EFFECTIVE_DATE = "March 6, 2026";
const LAST_UPDATED = "March 6, 2026";

const TOC_SECTIONS = [
    { id: "information-we-collect", label: "Information We Collect" },
    { id: "how-we-use-information", label: "How We Use Your Information" },
    { id: "cookies-and-tracking", label: "Cookies & Tracking" },
    { id: "google-adsense-advertising", label: "Google AdSense & Ads" },
    { id: "third-party-services", label: "Third-Party Services" },
    { id: "data-retention", label: "Data Retention" },
    { id: "your-privacy-rights", label: "Your Privacy Rights" },
    { id: "childrens-privacy", label: "Children's Privacy" },
    { id: "international-data-transfers", label: "International Transfers" },
    { id: "contact-us", label: "Contact Us" },
];

const INFO_ITEMS = [
    {
        term: "Personal Information:",
        description:
            "Such as your name, email address, and financial details provided when using the calculator.",
    },
    {
        term: "Usage Data:",
        description:
            "Information about how you navigate and interact with our site, including IP address, browser type, and pages visited.",
    },
];

const HOW_WE_USE_ITEMS = [
    "To provide and maintain the Nomad Tax Calculator service.",
    "To improve our website, services, and user experience.",
    "To communicate with you, including responding to inquiries and sending updates.",
    "To display relevant advertisements via Google AdSense and personalise ad content based on your prior visits.",
    "To analyse site traffic and user behaviour through Google Analytics.",
    "To comply with legal obligations and enforce our terms.",
];

const THIRD_PARTY_ITEMS = [
    "Google Analytics — to understand site usage patterns.",
    "Google AdSense & DoubleClick — to serve personalised advertisements.",
    "Payment processors — if and when payment features are offered.",
];

const DATA_RETENTION_ITEMS = [
    "Anonymous calculator sessions are retained for up to 30 days.",
    "If you create an account, your saved calculations are stored until you delete your account.",
    "Server logs are retained for up to 90 days for security purposes.",
];

const RIGHTS_ITEMS = [
    {
        term: "Right to Access —",
        description: "Request a copy of the personal data we hold about you.",
    },
    {
        term: "Right to Rectification —",
        description: "Request correction of inaccurate or incomplete data.",
    },
    {
        term: "Right to Erasure —",
        description:
            'Request deletion of your personal data ("right to be forgotten").',
    },
    {
        term: "Right to Restrict Processing —",
        description: "Request that we limit how we use your data.",
    },
    {
        term: "Right to Data Portability —",
        description: "Request your data in a portable format.",
    },
    {
        term: "Right to Object —",
        description:
            "Object to our processing of your data for advertising purposes.",
    },
    {
        term: "Right to Opt Out (CCPA) —",
        description:
            "California residents may opt out of the sale of personal information. We do not sell personal information.",
    },
];

// ---------------------------------------------------------------------------
// Page Component
// ---------------------------------------------------------------------------

export default function PrivacyPolicy() {
    const { mailConfig } = usePage().props;
    return (
        <TaxCalculatorLayout title="Privacy Policy">
            <Head>
                <title>Privacy Policy — Nomad Tax Calculator</title>
                <meta
                    name="description"
                    content="Read the Nomad Tax Calculator privacy policy to learn how we collect, use, and protect your personal information."
                />
            </Head>

            {/* ─── Page Hero ─────────────────────────────────────────── */}
            <div className="bg-primary text-light py-12 px-6 md:px-20 lg:px-40">
                <div className="max-w-5xl mx-auto">
                    <Breadcrumbs
                        items={[{ label: "Privacy Policy" }]}
                        className="mb-4 text-light/60 [&_a]:text-light/60 [&_a:hover]:text-light [&_svg]:text-light/40"
                    />
                    <h1 className="text-3xl md:text-4xl font-bold tracking-tight mb-2">
                        Privacy Policy
                    </h1>
                    <p className="text-sm text-light/60">
                        Effective Date: {EFFECTIVE_DATE}&nbsp;·&nbsp;Last
                        Updated: {LAST_UPDATED}
                    </p>
                </div>
            </div>

            {/* ─── Main content area ─────────────────────────────────── */}
            <div className="px-6 md:px-20 lg:px-40 py-10 md:py-16">
                <div className="max-w-5xl mx-auto">
                    {/* Intro paragraphs */}
                    <p className="text-sm text-gray leading-relaxed mb-3 max-w-2xl">
                        Welcome to Nomad Tax Calculator. This Privacy Policy
                        outlines how we collect, use, disclose, and protect your
                        personal information when you use our website and
                        services. By using this site, you agree to the practices
                        described below. If you do not agree, please discontinue
                        use of the site.
                    </p>
                    <p className="text-sm text-gray leading-relaxed mb-8 max-w-2xl">
                        This site uses Google AdSense to display advertisements.
                        As required by Google's policies, we disclose that
                        third-party vendors, including Google, use cookies to
                        serve ads based on your prior visits to this or other
                        websites.
                    </p>

                    {/* Disclaimer */}
                    <DisclaimerBanner className="mb-10" />

                    {/* ── Two-column layout: content (left) + sticky TOC (right) ──
                        `self-stretch` on the right column is the key fix — it makes
                        the wrapper div as tall as the content column, giving the inner
                        `sticky` element room to actually scroll within its parent.    */}
                    <div className="flex flex-col lg:flex-row gap-10 lg:gap-16 items-start">
                        {/* ── Left: Policy sections ── */}
                        <div className="flex-1 min-w-0 space-y-10">
                            {/* 1 ── Information We Collect */}
                            <PolicySection
                                id="information-we-collect"
                                index={1}
                                title="Information We Collect"
                            >
                                <Lead>
                                    We may collect the following types of
                                    information:
                                </Lead>
                                <PolicyList items={INFO_ITEMS} />
                            </PolicySection>

                            <PolicyDivider />

                            {/* 2 ── How We Use Your Information */}
                            <PolicySection
                                id="how-we-use-information"
                                index={2}
                                title="How We Use Your Information"
                            >
                                <Lead>
                                    The information we collect is used for the
                                    following purposes:
                                </Lead>
                                <PolicyList items={HOW_WE_USE_ITEMS} />
                            </PolicySection>

                            <PolicyDivider />

                            {/* 3 ── Cookies & Tracking */}
                            <PolicySection
                                id="cookies-and-tracking"
                                index={3}
                                title="Cookies and Tracking Technologies"
                            >
                                <Lead>
                                    We use cookies and similar tracking
                                    technologies to track activity on our
                                    service and hold certain information. You
                                    can instruct your browser to refuse all
                                    cookies or to indicate when a cookie is
                                    being sent.
                                </Lead>
                            </PolicySection>

                            <PolicyDivider />

                            {/* 4 ── Google AdSense & Personalized Advertising */}
                            <PolicySection
                                id="google-adsense-advertising"
                                index={4}
                                title="Google AdSense & Personalized Advertising"
                            >
                                <Lead>
                                    We use Google AdSense to display
                                    advertisements on this website. Google
                                    AdSense uses the{" "}
                                    <strong className="font-semibold text-primary">
                                        DoubleClick cookie
                                    </strong>{" "}
                                    to serve ads based on your prior visits to
                                    this website or other websites on the
                                    internet.
                                </Lead>
                                <Lead>
                                    Google's use of advertising cookies enables
                                    it and its partners to serve ads based on
                                    your visits to this and other sites. You may
                                    opt out of personalised advertising by
                                    visiting{" "}
                                    <a
                                        href="https://adssettings.google.com"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="text-primary underline underline-offset-2 font-medium hover:opacity-75 transition-opacity"
                                    >
                                        Google Ads Settings
                                    </a>
                                    . You can also opt out via the{" "}
                                    <a
                                        href="https://optout.networkadvertising.org/"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="text-primary underline underline-offset-2 font-medium hover:opacity-75 transition-opacity"
                                    >
                                        Network Advertising Initiative opt-out
                                        page
                                    </a>{" "}
                                    or the{" "}
                                    <a
                                        href="https://optout.aboutads.info/"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="text-primary underline underline-offset-2 font-medium hover:opacity-75 transition-opacity"
                                    >
                                        Digital Advertising Alliance opt-out
                                        tool
                                    </a>
                                    .
                                </Lead>
                                <Lead>
                                    We also use Google Analytics to understand
                                    how visitors interact with our site. Google
                                    Analytics collects data such as your IP
                                    address, browser type, pages visited, and
                                    time on site. This data is anonymised and
                                    used solely for improving our service. You
                                    can opt out of Google Analytics tracking by
                                    installing the{" "}
                                    <a
                                        href="https://tools.google.com/dlpage/gaoptout"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="text-primary underline underline-offset-2 font-medium hover:opacity-75 transition-opacity"
                                    >
                                        Google Analytics Opt-out Browser Add-on
                                    </a>
                                    .
                                </Lead>
                            </PolicySection>

                            <PolicyDivider />

                            {/* 5 ── Third-Party Services */}
                            <PolicySection
                                id="third-party-services"
                                index={5}
                                title="Third-Party Services"
                            >
                                <Lead>
                                    We may employ third-party companies and
                                    individuals to facilitate our service,
                                    provide the service on our behalf, or assist
                                    us in analysing how our service is used.
                                    These third parties have access to your
                                    personal data only to perform these tasks on
                                    our behalf and are obligated not to disclose
                                    or use it for any other purpose.
                                </Lead>
                                <Lead>
                                    Third-party services we currently use:
                                </Lead>
                                <PolicyList items={THIRD_PARTY_ITEMS} />
                            </PolicySection>

                            <PolicyDivider />

                            {/* 6 ── Data Retention */}
                            <PolicySection
                                id="data-retention"
                                index={6}
                                title="Data Retention"
                            >
                                <Lead>
                                    We retain your data only as long as
                                    necessary to provide our service:
                                </Lead>
                                <PolicyList items={DATA_RETENTION_ITEMS} />
                                <Lead>
                                    You may request deletion of your data at any
                                    time by contacting us at{" "}
                                    <a
                                        href={`mailto:${mailConfig.from}`}
                                        className="text-primary underline underline-offset-2 font-medium hover:opacity-75 transition-opacity"
                                    >
                                        {mailConfig.from}
                                    </a>
                                    .
                                </Lead>
                            </PolicySection>

                            <PolicyDivider />

                            {/* 7 ── Your Privacy Rights */}
                            <PolicySection
                                id="your-privacy-rights"
                                index={7}
                                title="Your Privacy Rights"
                            >
                                <Lead>
                                    Depending on your location, you may have the
                                    following rights regarding your personal
                                    data:
                                </Lead>
                                <PolicyList items={RIGHTS_ITEMS} />
                                <Lead>
                                    To exercise any of these rights, contact us
                                    at{" "}
                                    <a
                                        href={`mailto:${mailConfig.from}`}
                                        className="text-primary underline underline-offset-2 font-medium hover:opacity-75 transition-opacity"
                                    >
                                        {mailConfig.from}
                                    </a>
                                    . We will respond within 30 days.
                                </Lead>
                            </PolicySection>

                            <PolicyDivider />

                            {/* 8 ── Children's Privacy */}
                            <PolicySection
                                id="childrens-privacy"
                                index={8}
                                title="Children's Privacy"
                            >
                                <Lead>
                                    This website is not directed to children
                                    under the age of 13. We do not knowingly
                                    collect personal information from children
                                    under 13. If you are a parent or guardian
                                    and believe your child has provided us with
                                    personal information, please contact us
                                    immediately at{" "}
                                    <a
                                        href={`mailto:${mailConfig.from}`}
                                        className="text-primary underline underline-offset-2 font-medium hover:opacity-75 transition-opacity"
                                    >
                                        {mailConfig.from}
                                    </a>{" "}
                                    and we will delete that information
                                    promptly.
                                </Lead>
                            </PolicySection>

                            <PolicyDivider />

                            {/* 9 ── International Data Transfers */}
                            <PolicySection
                                id="international-data-transfers"
                                index={9}
                                title="International Data Transfers"
                            >
                                <Lead>
                                    If you are located in the European Economic
                                    Area (EEA), United Kingdom, or Switzerland,
                                    your data may be transferred to and
                                    processed in countries outside your region,
                                    including the United States, where data
                                    protection laws may differ.
                                </Lead>
                                <Lead>
                                    We ensure appropriate safeguards are in
                                    place for such transfers in accordance with
                                    applicable law, including Standard
                                    Contractual Clauses where required.
                                </Lead>
                            </PolicySection>

                            <PolicyDivider />

                            {/* 10 ── Contact Us */}
                            <PolicySection
                                id="contact-us"
                                index={10}
                                title="Contact Us"
                            >
                                <Lead>
                                    If you have any questions or concerns about
                                    this Privacy Policy or our data practices,
                                    please contact us.
                                </Lead>
                                <ContactCard fromEmail={mailConfig.from} />
                            </PolicySection>
                        </div>

                        {/* ── Right: Sticky Table of Contents ──────────────────────
                            `self-stretch` makes this column as tall as the flex row
                            (i.e. the content column), so the inner sticky aside has
                            a full-height parent to scroll within — this is the fix.  */}
                        <div className="hidden lg:block w-56 xl:w-64 flex-shrink-0 self-stretch">
                            <TableOfContents sections={TOC_SECTIONS} />
                        </div>
                    </div>
                </div>
            </div>
        </TaxCalculatorLayout>
    );
}
