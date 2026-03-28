import { useRef } from "react";
import gsap from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { useGSAP } from "@gsap/react";
import { ScrollSmoother } from "gsap/ScrollSmoother";

gsap.registerPlugin(ScrollTrigger , ScrollSmoother);

export default function useStackedCards() {
    const landingPageWrapper = useRef();
    const landingPageContent = useRef();
    const container = useRef();

    useGSAP(
        () => {
            const cards = gsap.utils.toArray(".stack-card");
            ScrollSmoother.create({
                wrapper: landingPageWrapper.current,
                content: landingPageContent.current,
                smooth: 1.2,
                effects: true,
                smoothTouch: 0.1,
            });
        },
        { scope: container }, // automatic cleanup
    );

    return {container , landingPageWrapper, landingPageContent};
}
