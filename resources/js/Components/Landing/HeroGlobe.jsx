import { useEffect, useRef, useState, useCallback } from "react";
import createGlobe from "cobe";

const DESTINATIONS = [
  { id: "th", name: "Thailand",  city: "Chiang Mai",  location: [18.79,  98.99],  size: 0.06, color: [1, 0.6, 0.1],    tax: "0% foreign income" },
  { id: "pt", name: "Portugal",  city: "Lisbon",      location: [38.72,  -9.14],  size: 0.06, color: [0.18, 0.55, 1],  tax: "NHR regime"        },
  { id: "id", name: "Indonesia", city: "Bali",        location: [-8.34,  115.09], size: 0.06, color: [1, 0.3, 0.3],    tax: "Nomad visa"        },
  { id: "mx", name: "Mexico",    city: "Mexico City", location: [19.43,  -99.13], size: 0.06, color: [0.15, 0.8, 0.45],tax: "183-day rule"      },
  { id: "ge", name: "Georgia",   city: "Tbilisi",     location: [41.69,  44.80],  size: 0.06, color: [1, 0.75, 0.1],   tax: "1% flat tax"       },
  { id: "ae", name: "UAE",       city: "Dubai",       location: [25.20,  55.27],  size: 0.06, color: [0.65, 0.35, 1],  tax: "0% income tax"     },
];

function FlagPill({ dest, isActive, onClick }) {
  const [err, setErr] = useState(false);
  const rgb = dest.color.map((c) => Math.round(c * 255)).join(",");
  const activeStyle = {
    border: `1.5px solid rgba(${rgb},0.8)`,
    background: `rgba(${rgb},0.1)`,
    boxShadow: `0 2px 12px rgba(${rgb},0.22)`,
    transform: "scale(1.06)",
  };
  const inactiveStyle = {
    border: "1.5px solid rgba(0,0,0,0.09)",
    background: "rgba(255,255,255,0.75)",
    boxShadow: "0 1px 3px rgba(0,0,0,0.06)",
    transform: "scale(1)",
  };

  return (
    <button
      onClick={onClick}
      className="flex items-center gap-1.5 px-3 py-1 rounded-full cursor-pointer outline-none shrink-0 transition-all duration-200"
      style={isActive ? activeStyle : inactiveStyle}
    >
      {!err ? (
        <img
          src={`https://flagcdn.com/24x18/${dest.id}.png`}
          alt={dest.name} width="20" height="15"
          onError={() => setErr(true)}
          className="rounded-sm object-cover shrink-0 block"
        />
      ) : <span className="text-[13px]">🌍</span>}
      <span 
        className="text-[11px] font-bold whitespace-nowrap tracking-[0.2px]"
        style={{ color: isActive ? `rgb(${rgb})` : "#555" }}
      >
        {dest.city}
      </span>
    </button>
  );
}

export default function HeroGlobe() {
  const canvasRef     = useRef(null);
  const globeRef      = useRef(null);
  const rafRef        = useRef(null);
  const phiRef        = useRef(0);
  const thetaRef      = useRef(0.25);
  const isDragging    = useRef(false);
  const lastPointer   = useRef({ x: 0, y: 0 });
  const velocityRef   = useRef({ x: 0, y: 0 });
  const autoRotate    = useRef(true);
  const resumeTimer   = useRef(null);

  const [active, setActive] = useState(1);
  const [dragging, setDragging] = useState(false);

  // Auto-cycle every 3.5s
  useEffect(() => {
    const id = setInterval(() => {
      if (autoRotate.current) setActive((p) => (p + 1) % DESTINATIONS.length);
    }, 3500);
    return () => clearInterval(id);
  }, []);

  // ── Pointer handlers ──────────────────────────────────────────────────────
  const onPointerDown = useCallback((e) => {
    isDragging.current  = true;
    autoRotate.current  = false;
    velocityRef.current = { x: 0, y: 0 };
    lastPointer.current = { x: e.clientX, y: e.clientY };
    canvasRef.current?.setPointerCapture(e.pointerId);
    setDragging(true);
    e.preventDefault();
  }, []);

  const onPointerMove = useCallback((e) => {
    if (!isDragging.current) return;
    const dx = e.clientX - lastPointer.current.x;
    const dy = e.clientY - lastPointer.current.y;
    lastPointer.current = { x: e.clientX, y: e.clientY };
    phiRef.current += dx * 0.008;
    thetaRef.current = Math.max(-0.85, Math.min(0.85, thetaRef.current + dy * 0.005));
    velocityRef.current = { x: dx, y: dy };
    e.preventDefault();
  }, []);

  const onPointerUp = useCallback(() => {
    if (!isDragging.current) return;
    isDragging.current = false;
    setDragging(false);
    clearTimeout(resumeTimer.current);
    resumeTimer.current = setTimeout(() => { autoRotate.current = true; }, 2000);
  }, []);

  // ── Globe init ────────────────────────────────────────────────────────────
  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;

    globeRef.current = createGlobe(canvas, {
      devicePixelRatio: 2,
      width:  480 * 2,
      height: 480 * 2,
      phi:    phiRef.current,
      theta:  thetaRef.current,
      dark:   0,
      diffuse: 1.8,
      mapSamples: 22000,
      mapBrightness: 8,
      mapBaseBrightness: 0.02,
      baseColor:   [0.92, 0.94, 1.0],
      markerColor: [0.3, 0.5, 1],
      glowColor:   [0.75, 0.84, 1.0],
      scale: 1.08,
      markers: DESTINATIONS.map((d) => ({
        location: d.location, size: d.size, color: d.color, id: d.id,
      })),
    });

    canvas.style.opacity = "1";

    function animate() {
      if (!isDragging.current) {
        if (Math.abs(velocityRef.current.x) > 0.1 || Math.abs(velocityRef.current.y) > 0.1) {
          phiRef.current   += velocityRef.current.x * 0.006;
          thetaRef.current  = Math.max(-0.85, Math.min(0.85,
            thetaRef.current + velocityRef.current.y * 0.004));
          velocityRef.current = {
            x: velocityRef.current.x * 0.90,
            y: velocityRef.current.y * 0.90,
          };
        }
        if (autoRotate.current) phiRef.current += 0.003;
      }
      globeRef.current.update({ phi: phiRef.current, theta: thetaRef.current });
      rafRef.current = requestAnimationFrame(animate);
    }
    animate();

    const onResize = () => {
      globeRef.current.update({ width: canvas.offsetWidth * 2, height: canvas.offsetWidth * 2 });
    };
    window.addEventListener("resize", onResize);

    return () => {
      cancelAnimationFrame(rafRef.current);
      clearTimeout(resumeTimer.current);
      globeRef.current.destroy();
      window.removeEventListener("resize", onResize);
    };
  }, []);

  const dest = DESTINATIONS[active];
  const rgb  = dest.color.map((c) => Math.round(c * 255)).join(",");

  return (
    <>
      {/* Inject dynamic CSS anchor styles into the document head for the active pointers */}
      <style>{`
        ${DESTINATIONS.map((d) => `
          .marker-label-${d.id} {
            position-anchor: --cobe-${d.id};
            opacity: var(--cobe-visible-${d.id}, 0);
            border: 1px solid rgba(${d.color.map((c) => Math.round(c * 255)).join(",")},0.45);
            box-shadow: 0 2px 10px rgba(${d.color.map((c) => Math.round(c * 255)).join(",")},0.2);
          }
        `).join("")}
      `}</style>

      <div className="w-full flex flex-col items-center gap-[18px] select-none">
        <div className="globe-wrapper">

          {/* Ambient glow */}
          <div 
            className="absolute inset-[10%] rounded-full pointer-events-none z-0 transition-[background] duration-[1100ms] ease-in-out"
            style={{
              background: `radial-gradient(circle, rgba(${rgb},0.14) 0%, transparent 68%)`,
            }} 
          />

          {/* Canvas */}
          <canvas
            ref={canvasRef}
            className={`globe-canvas ${dragging ? "cursor-grabbing" : "cursor-grab"}`}
            onPointerDown={onPointerDown}
            onPointerMove={onPointerMove}
            onPointerUp={onPointerUp}
            onPointerLeave={onPointerUp}
          />

          {/* CSS-anchor-positioned floating labels */}
          {DESTINATIONS.map((d) => (
            <div key={d.id} className={`cobe-marker-base marker-label-${d.id}`}>
              <img
                src={`https://flagcdn.com/24x18/${d.id}.png`}
                alt={d.name} width="16" height="12"
                className="rounded-sm object-cover block shrink-0"
              />
              {d.city}
            </div>
          ))}

          {/* Info pill */}
          <div className="absolute bottom-[5%] left-1/2 -translate-x-1/2 z-[12] pointer-events-none">
            <div 
              className="inline-flex items-center gap-2 bg-white/90 backdrop-blur-md rounded-full px-[14px] py-1 transition-[border-color,box-shadow] duration-700 whitespace-nowrap"
              style={{
                border: `1px solid rgba(${rgb},0.35)`,
                boxShadow: `0 4px 18px rgba(${rgb},0.18)`,
              }}
            >
              <span className="text-[11px] text-gray-500">📍</span>
              <span className="text-xs font-bold text-[#111] tracking-[0.3px]">
                {dest.city}, {dest.name}
              </span>
              <span 
                className="text-[10px] font-bold rounded-full px-2 py-0.5"
                style={{
                  color: `rgb(${rgb})`, background: `rgba(${rgb},0.1)`,
                }}
              >
                {dest.tax}
              </span>
            </div>
          </div>
        </div>

        {/* Flag pill selectors */}
        <div className="flex flex-wrap gap-[7px] justify-center max-w-[480px] w-full">
          {DESTINATIONS.map((d, i) => (
            <FlagPill key={d.id} dest={d} isActive={active === i} onClick={() => setActive(i)} />
          ))}
        </div>
      </div>
    </>
  );
}