import React from "react";
import AlexAvatar from "@/assets/images/avatars/alex_rivera.jpg";
import SarahAvatar from "@/assets/images/avatars/download.jpg";
import JamesAvatar from "@/assets/images/avatars/james_wright.jpg";

export default function TestimonialsSection({ testimonials }) {
    const avatarMap = {
        alex: AlexAvatar,
        sarah: SarahAvatar,
        james: JamesAvatar,
    };

    const renderStars = (rating) => {
        return Array.from({ length: rating }, (_, i) => (
            <svg
                key={i}
                className="w-5 h-5"
                fill="currentColor"
                viewBox="0 0 24 24"
            >
                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
            </svg>
        ));
    };

    return (
        <section
            id="testimonials"
            className="bg-light dark:bg-dark-surface px-6 py-24 lg:px-40"
        >
            <div className="max-w-[960px] mx-auto">
                <h2 className="text-primary dark:text-white text-[32px] font-bold text-center mb-16">
                    What Our Users Say
                </h2>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {testimonials &&
                        testimonials.map((testimonial) => (
                            <div
                                key={testimonial.id}
                                className="bg-white dark:bg-dark-elevated p-8 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center"
                            >
                                <div className="w-16 h-16 rounded-full overflow-hidden mb-4 bg-gray-100 border border-gray-200">
                                    <img
                                        src={avatarMap[testimonial.avatar]}
                                        alt={testimonial.name}
                                        className="w-full h-full object-cover"
                                    />
                                </div>

                                <div className="flex text-yellow-400 mb-4">
                                    {renderStars(testimonial.rating)}
                                </div>

                                <p className="text-gray dark:text-gray-300 italic mb-6 leading-relaxed">
                                    "{testimonial.testimonial}"
                                </p>

                                <div>
                                    <p className="font-bold text-primary dark:text-white">
                                        {testimonial.name}
                                    </p>
                                    <p className="text-xs text-gray uppercase tracking-wider mt-1">
                                        {testimonial.role}
                                    </p>
                                </div>
                            </div>
                        ))}
                </div>
            </div>
        </section>
    );
}
