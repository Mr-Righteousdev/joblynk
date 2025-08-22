import { Head, useForm } from '@inertiajs/react';
import { LoaderCircle, User, Briefcase } from 'lucide-react';
import { FormEventHandler, useState } from 'react';

import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthLayout from '@/layouts/auth-layout';

interface RegisterForm {
    [key: string]: any;  // Add index signature
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    purpose: string;
}

export default function Register() {
    // Shared state for form fields
    const [formFields, setFormFields] = useState({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    // Two separate form instances for each purpose
    const jobseekerForm = useForm<RegisterForm>({
        ...formFields,
        purpose: 'jobseeker',
    });

    const employerForm = useForm<RegisterForm>({
        ...formFields,
        purpose: 'employer',
    });

    const [activeForm, setActiveForm] = useState<'jobseeker' | 'employer' | null>(null);

    // Update both forms when fields change
    const updateField = (field: keyof typeof formFields, value: string) => {
        const newFields = { ...formFields, [field]: value };
        setFormFields(newFields);
        
        jobseekerForm.setData({ ...newFields, purpose: 'jobseeker' });
        employerForm.setData({ ...newFields, purpose: 'employer' });
    };

    const submitJobseeker: FormEventHandler = (e) => {
        e.preventDefault();
        setActiveForm('jobseeker');
        jobseekerForm.post(route('register'), {
            onFinish: () => {
                jobseekerForm.reset('password', 'password_confirmation');
                employerForm.reset('password', 'password_confirmation');
                setActiveForm(null);
            },
        });
    };

    const submitEmployer: FormEventHandler = (e) => {
        e.preventDefault();
        setActiveForm('employer');
        employerForm.post(route('register'), {
            onFinish: () => {
                jobseekerForm.reset('password', 'password_confirmation');
                employerForm.reset('password', 'password_confirmation');
                setActiveForm(null);
            },
        });
    };

    const processing = jobseekerForm.processing || employerForm.processing;
    const errors = activeForm === 'jobseeker' ? jobseekerForm.errors : employerForm.errors;

    return (
        <AuthLayout title="Create an account" description="Enter your details below to create your account">
            <Head title="Register" />
            <div className="flex flex-col gap-6">
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
                            value={formFields.name}
                            onChange={(e) => updateField('name', e.target.value)}
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
                            value={formFields.email}
                            onChange={(e) => updateField('email', e.target.value)}
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
                            value={formFields.password}
                            onChange={(e) => updateField('password', e.target.value)}
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
                            value={formFields.password_confirmation}
                            onChange={(e) => updateField('password_confirmation', e.target.value)}
                            disabled={processing}
                            placeholder="Confirm password"
                        />
                        <InputError message={errors.password_confirmation} />
                    </div>

                    <div className="flex gap-3">
                        <form onSubmit={submitJobseeker} className="w-full">
                            <Button 
                                type="submit"
                                className="mt-2 flex w-full gap-2 bg-blue-500 hover:bg-blue-600" 
                                tabIndex={5} 
                                disabled={processing}
                            >
                                {processing && activeForm === 'jobseeker' ? (
                                    <LoaderCircle className="h-4 w-4 animate-spin" />
                                ) : (
                                    <User className="h-4 w-4" />
                                )}
                                Create jobseeker account
                            </Button>
                        </form>

                        <form onSubmit={submitEmployer} className="w-full">
                            <Button 
                                type="submit"
                                className="mt-2 flex w-full gap-2 bg-green-500 hover:bg-green-600" 
                                tabIndex={6} 
                                disabled={processing}
                            >
                                {processing && activeForm === 'employer' ? (
                                    <LoaderCircle className="h-4 w-4 animate-spin" />
                                ) : (
                                    <Briefcase className="h-4 w-4" />
                                )}
                                Create employer account
                            </Button>
                        </form>
                    </div>

                    <InputError message={errors.purpose} />
                </div>

                <div className="text-muted-foreground text-center text-sm">
                    Already have an account?{' '}
                    <TextLink href={route('login')} tabIndex={7}>
                        Log in
                    </TextLink>
                </div>
            </div>
        </AuthLayout>
    );
}