import Checkbox from "@/Components/Checkbox";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import GuestLayout from "@/Layouts/GuestLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import { Eye, EyeOff } from "lucide-react";
import { useState } from "react";
import googleLogo from "@/assets/images/logos/google.svg";

export default function Login({ status, canResetPassword }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: "",
        password: "",
        remember: false,
    });

    const [showPassword, setShowPassword] = useState(false);

    const submit = (e) => {
        e.preventDefault();

        post(route("login"), {
            onFinish: () => reset("password"),
        });
    };

    return (
        <GuestLayout>
            <Head title="Log in" />

            {status && (
                <div className="mb-4 text-sm font-medium text-green-600">
                    {status}
                </div>
            )}

            <form onSubmit={submit}>
                <div>
                    <InputLabel htmlFor="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        isFocused={true}
                        onChange={(e) => setData("email", e.target.value)}
                    />

                    <InputError message={errors.email} className="mt-2" />
                </div>

                <div className="mt-4">
                    <div className="flex items-center justify-between">
                        <InputLabel htmlFor="password" value="Password" />
                        {canResetPassword && (
                            <Link
                                href={route("password.request")}
                                className="text-xs text-gray underline hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 rounded-md"
                            >
                                Forgot password?
                            </Link>
                        )}
                    </div>

                    <div className="relative">
                        <TextInput
                            id="password"
                            type={showPassword ? "text" : "password"}
                            name="password"
                            value={data.password}
                            className="mt-1 block w-full pr-10"
                            autoComplete="current-password"
                            onChange={(e) =>
                                setData("password", e.target.value)
                            }
                        />
                        <button
                            type="button"
                            onClick={() => setShowPassword(!showPassword)}
                            className="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 hover:text-gray-700 focus:outline-none mt-1"
                        >
                            {showPassword ? (
                                <EyeOff className="h-5 w-5 text-gray" />
                            ) : (
                                <Eye className="h-5 w-5 text-gray" />
                            )}
                        </button>
                    </div>

                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="mt-4 block">
                    <label className="flex items-center">
                        <Checkbox
                            name="remember"
                            checked={data.remember}
                            onChange={(e) =>
                                setData("remember", e.target.checked)
                            }
                        />
                        <span className="ms-2 text-sm text-gray">
                            Remember me
                        </span>
                    </label>
                </div>

                <div className="mt-4 flex flex-col md:flex-row items-center justify-between gap-4">
                    <Link
                        href={route("register")}
                        className="rounded-md text-sm text-gray underline hover:text-primary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                    >
                        Don't have an account? Register
                    </Link>

                    <PrimaryButton disabled={processing}>Log in</PrimaryButton>
                </div>
            </form>

            {/* ── Or continue with ── */}
            <div className="mt-6 flex items-center gap-3">
                <hr className="flex-1 border-gray-200" />
                <span className="text-xs text-gray-400">or continue with</span>
                <hr className="flex-1 border-gray-200" />
            </div>

            <a
                href={route("auth.google.redirect")}
                className="mt-4 flex w-full items-center justify-center gap-3 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 hover:shadow focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
                <img src={googleLogo} alt="Google" className="h-5 w-5" />
                Continue with Google
            </a>
        </GuestLayout>
    );
}
