import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle } from 'lucide-react';
import { FormEventHandler } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

import { Briefcase, User } from "lucide-react";

interface RegisterForm {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    purpose?: 'jobseeker' | 'employer'; // Made optional
}

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm<RegisterForm>({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        // Don't initialize purpose - it will be set on button click
    });

    const handleSubmit = (purpose: 'jobseeker' | 'employer'): FormEventHandler => (e) => {
        e.preventDefault();
        
        // Include purpose directly in the request data
        post(route('register'), {
            data: { ...data, purpose },
            onFinish: () => reset('password', 'password_confirmation'),
        });
    };

    // Alternative approach - you could also set purpose first, then submit
    const handleSubmitAlternative = (purpose: 'jobseeker' | 'employer') => (e: React.FormEvent) => {
        e.preventDefault();
        
        // First set the purpose, then submit in the callback
        setData('purpose', purpose);
        
        // Use setTimeout to ensure state is updated (not ideal, but works)
        setTimeout(() => {
            post(route('register'), {
                onFinish: () => reset('password', 'password_confirmation'),
            });
        }, 0);
    };

    return (
        <AuthLayout title="Create an account" description="Enter your details below to create your account">
            <Head title="Register" />
            <form className="flex flex-col gap-6">
                <div className="grid gap-6">
                    <div className="grid gap-2">
                        <Label htmlFor="name">Name</Label>
                        <Input
                            id="name"
                            type="text"
                            required
                            autoFocus
                            tabIndex={1}
                            autoComplete="name"
                            value={data.name}
                            onChange={(e) => setData('name', e.target.value)}
                            disabled={processing}
                            placeholder="Full name"
                        />
                        <InputError message={errors.name} className="mt-2" />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="email">Email address</Label>
                        <Input
                            id="email"
                            type="email"
                            required
                            tabIndex={2}
                            autoComplete="email"
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            disabled={processing}
                            placeholder="email@example.com"
                        />
                        <InputError message={errors.email} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="password">Password</Label>
                        <Input
                            id="password"
                            type="password"
                            required
                            tabIndex={3}
                            autoComplete="new-password"
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            disabled={processing}
                            placeholder="Password"
                        />
                        <InputError message={errors.password} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="password_confirmation">Confirm password</Label>
                        <Input
                            id="password_confirmation"
                            type="password"
                            required
                            tabIndex={4}
                            autoComplete="new-password"
                            value={data.password_confirmation}
                            onChange={(e) => setData('password_confirmation', e.target.value)}
                            disabled={processing}
                            placeholder="Confirm password"
                        />
                        <InputError message={errors.password_confirmation} />
                    </div>

                    <div className="flex gap-2">
                        <Button
                            type="submit"
                            className="mt-2 flex w-full gap-2 bg-blue-500 hover:bg-blue-600"
                            tabIndex={5}
                            disabled={processing}
                            onClick={handleSubmit('jobseeker')}
                        >
                            {processing ? (
                                <LoaderCircle className="h-4 w-4 animate-spin" />
                            ) : (
                                <User className="h-4 w-4" />
                            )}
                            Register as Jobseeker
                        </Button>

                        <Button
                            type="submit"
                            className="mt-2 flex w-full gap-2 bg-green-500 hover:bg-green-600"
                            tabIndex={6}
                            disabled={processing}
                            onClick={handleSubmit('employer')}
                        >
                            {processing ? (
                                <LoaderCircle className="h-4 w-4 animate-spin" />
                            ) : (
                                <Briefcase className="h-4 w-4" />
                            )}
                            Register as Employer
                        </Button>
                    </div>
                    
                    {/* Add error display for purpose field */}
                    <InputError message={errors.purpose} />
                </div>

                <div className="text-muted-foreground text-center text-sm">
                    Already have an account?{' '}
                    <TextLink href={route('login')} tabIndex={7}>
                        Log in
                    </TextLink>
                </div>
            </form>
        </AuthLayout>
    );
}